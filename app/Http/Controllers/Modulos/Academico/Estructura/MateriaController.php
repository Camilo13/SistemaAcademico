<?php

namespace App\Http\Controllers\Modulos\Academico\Estructura;

use App\Http\Controllers\Controller;
use App\Models\Grado;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class MateriaController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Materia::with(['grado.sede']);

        if ($request->filled('grado')) {
            $query->where('grado_id', $request->grado);
        }

        if ($request->filled('estado')) {
            $query->where('activa', $request->estado === 'activa');
        }

        $materias = $query
            ->orderBy('grado_id')
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        // get() con sede para poder mostrar sede en el filtro
        $grados = Grado::with('sede')->orderBy('nombre')->get();

        return view(
            'modulos.academico.estructura.materia.index',
            compact('materias', 'grados')
        );
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create()
    {
        // get() con sede — las vistas usan $grado->sede->nombre
        $grados = Grado::with('sede')->activos()->orderBy('nombre')->get();

        return view(
            'modulos.academico.estructura.materia.create',
            compact('grados')
        );
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->merge([
            'codigo' => trim($request->codigo ?? ''),
            'nombre' => trim($request->nombre ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglasStore(),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($request, $validated) {
                Materia::create([
                    'grado_id'           => $validated['grado_id'],
                    'codigo'             => $validated['codigo'] ?: null,
                    'nombre'             => $validated['nombre'],
                    'intensidad_horaria' => $validated['intensidad_horaria'] ?? null,
                    'activa'             => $request->boolean('activa', true),
                ]);
            });

            return redirect()
                ->route('admin.academico.estructura.materias.index')
                ->with('exito', 'Materia creada correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_materia' => 'Ocurrió un error al crear la materia.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(Materia $materia)
    {
        $grados = Grado::with('sede')->activos()->orderBy('nombre')->get();

        return view(
            'modulos.academico.estructura.materia.edit',
            compact('materia', 'grados')
        );
    }

    /*
    |----------------------------------------------------------------------
    | update — Solo datos (grado, codigo, nombre, intensidad)
    | La visibilidad se maneja con activar/desactivar
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Materia $materia)
    {
        $request->merge([
            'codigo' => trim($request->codigo ?? ''),
            'nombre' => trim($request->nombre ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglasUpdate($materia),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($materia, $validated) {
                $materia->update([
                    'grado_id'           => $validated['grado_id'],
                    'codigo'             => $validated['codigo'] ?: null,
                    'nombre'             => $validated['nombre'],
                    'intensidad_horaria' => $validated['intensidad_horaria'] ?? null,
                ]);
            });

            return redirect()
                ->route('admin.academico.estructura.materias.index')
                ->with('exito', 'Materia actualizada correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_materia' => 'Ocurrió un error al actualizar la materia.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | activar
    |----------------------------------------------------------------------
    */
    public function activar(Materia $materia)
    {
        try {

            DB::transaction(fn () => $materia->activar());

            return back()->with('exito', "La materia \"{$materia->nombre}\" fue activada.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_materia' => 'No fue posible activar la materia.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | desactivar
    |----------------------------------------------------------------------
    */
    public function desactivar(Materia $materia)
    {
        try {

            DB::transaction(fn () => $materia->desactivar());

            return back()->with('exito', "La materia \"{$materia->nombre}\" fue desactivada.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_materia' => 'No fue posible desactivar la materia.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(Materia $materia)
    {
        // Pre-check: no eliminar si tiene asignaciones
        if ($materia->asignaciones()->exists()) {
            return back()->withErrors([
                'error_materia' =>
                    "No se puede eliminar la materia \"{$materia->nombre}\" porque tiene " .
                    $materia->asignaciones()->count() . " asignación(es) registrada(s).",
            ]);
        }

        try {

            $nombre = $materia->nombre;

            DB::transaction(fn () => $materia->delete());

            return redirect()
                ->route('admin.academico.estructura.materias.index')
                ->with('exito', "Materia \"{$nombre}\" eliminada correctamente.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_materia' => $e->getMessage()]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | Reglas — store
    |----------------------------------------------------------------------
    */
    private function reglasStore(): array
    {
        return [
            'grado_id' => ['required', 'exists:grados,id'],
            'codigo' => [
                'nullable', 'string', 'max:20',
                Rule::unique('materias')->where(
                    fn ($q) => $q->where('grado_id', request('grado_id'))
                ),
            ],
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('materias')->where(
                    fn ($q) => $q->where('grado_id', request('grado_id'))
                ),
            ],
            'intensidad_horaria' => ['nullable', 'integer', 'min:1', 'max:40'],
            'activa'             => ['nullable', 'boolean'],
        ];
    }

    /*
    |----------------------------------------------------------------------
    | Reglas — update
    |----------------------------------------------------------------------
    */
    private function reglasUpdate(Materia $materia): array
    {
        return [
            'grado_id' => ['required', 'exists:grados,id'],
            'codigo' => [
                'nullable', 'string', 'max:20',
                Rule::unique('materias')
                    ->where(fn ($q) => $q->where('grado_id', request('grado_id')))
                    ->ignore($materia->id),
            ],
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('materias')
                    ->where(fn ($q) => $q->where('grado_id', request('grado_id')))
                    ->ignore($materia->id),
            ],
            'intensidad_horaria' => ['nullable', 'integer', 'min:1', 'max:40'],
        ];
    }

    /*
    |----------------------------------------------------------------------
    | Mensajes
    |----------------------------------------------------------------------
    */
    private function mensajes(): array
    {
        return [
            'grado_id.required'          => 'Debe seleccionar un grado.',
            'grado_id.exists'            => 'El grado seleccionado no es válido.',
            'codigo.max'                 => 'El código no puede superar los 20 caracteres.',
            'codigo.unique'              => 'Ya existe ese código en el grado seleccionado.',
            'nombre.required'            => 'El nombre de la materia es obligatorio.',
            'nombre.max'                 => 'El nombre no puede superar los 100 caracteres.',
            'nombre.unique'              => 'Ya existe una materia con ese nombre en el grado.',
            'intensidad_horaria.integer' => 'La intensidad horaria debe ser un número.',
            'intensidad_horaria.min'     => 'La intensidad mínima es 1 hora.',
            'intensidad_horaria.max'     => 'La intensidad máxima es 40 horas.',
        ];
    }
}
