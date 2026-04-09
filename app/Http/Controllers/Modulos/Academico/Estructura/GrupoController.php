<?php

namespace App\Http\Controllers\Modulos\Academico\Estructura;

use App\Http\Controllers\Controller;
use App\Models\AnioLectivo;
use App\Models\Grado;
use App\Models\Grupo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class GrupoController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Grupo::with(['grado.sede', 'anioLectivo']);

        if ($request->filled('anio')) {
            $query->where('anio_lectivo_id', $request->anio);
        }

        if ($request->filled('grado')) {
            $query->where('grado_id', $request->grado);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $grupos = $query
            ->orderByDesc('anio_lectivo_id')
            ->orderBy('grado_id')
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $grados = Grado::with('sede')->ordenados()->get();
        $anios  = AnioLectivo::orderByDesc('fecha_inicio')->get();

        return view(
            'modulos.academico.estructura.grupo.index',
            compact('grupos', 'grados', 'anios')
        );
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create()
    {
        $grados = Grado::with('sede')->activos()->ordenados()->get();
        $anios  = AnioLectivo::orderByDesc('fecha_inicio')->get();

        return view(
            'modulos.academico.estructura.grupo.create',
            compact('grados', 'anios')
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
            'nombre' => strtoupper(trim($request->nombre ?? '')),
        ]);

        $validated = $request->validate(
            $this->reglasStore(),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($request, $validated) {
                Grupo::create([
                    'grado_id'        => $validated['grado_id'],
                    'anio_lectivo_id' => $validated['anio_lectivo_id'],
                    'nombre'          => $validated['nombre'],
                    'cupo_maximo'     => $validated['cupo_maximo'] ?? null,
                    'activo'          => $request->boolean('activo', true),
                ]);
            });

            return redirect()
                ->route('admin.academico.estructura.grupos.index')
                ->with('exito', 'Grupo creado correctamente.');

        } catch (QueryException $e) {

            return back()
                ->withErrors(['nombre' => 'Ya existe un grupo con ese identificador para ese grado y año lectivo.'])
                ->withInput();

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_grupo' => 'Ocurrió un error al crear el grupo.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(Grupo $grupo)
    {
        $grados = Grado::with('sede')->activos()->ordenados()->get();
        $anios  = AnioLectivo::orderByDesc('fecha_inicio')->get();

        return view(
            'modulos.academico.estructura.grupo.edit',
            compact('grupo', 'grados', 'anios')
        );
    }

    /*
    |----------------------------------------------------------------------
    | update — Solo datos (grado, año, nombre, cupo)
    | La visibilidad se maneja con activar/desactivar
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Grupo $grupo)
    {
        $request->merge([
            'nombre' => strtoupper(trim($request->nombre ?? '')),
        ]);

        $validated = $request->validate(
            $this->reglasUpdate($grupo),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($grupo, $validated) {

                // Validar que el nuevo cupo no sea menor a los inscritos actuales
                if (
                    isset($validated['cupo_maximo']) &&
                    $grupo->inscripciones()->count() > $validated['cupo_maximo']
                ) {
                    throw new RuntimeException(
                        'El cupo máximo (' . $validated['cupo_maximo'] . ') no puede ser menor ' .
                        'al número actual de inscritos (' . $grupo->inscripciones()->count() . ').'
                    );
                }

                $grupo->update([
                    'grado_id'        => $validated['grado_id'],
                    'anio_lectivo_id' => $validated['anio_lectivo_id'],
                    'nombre'          => $validated['nombre'],
                    'cupo_maximo'     => $validated['cupo_maximo'] ?? null,
                ]);
            });

            return redirect()
                ->route('admin.academico.estructura.grupos.index')
                ->with('exito', 'Grupo actualizado correctamente.');

        } catch (QueryException $e) {

            return back()
                ->withErrors(['nombre' => 'Ya existe un grupo con ese identificador para ese grado y año lectivo.'])
                ->withInput();

        } catch (RuntimeException $e) {

            return back()
                ->withErrors(['error_grupo' => $e->getMessage()])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | activar
    |----------------------------------------------------------------------
    */
    public function activar(Grupo $grupo)
    {
        try {

            DB::transaction(fn () => $grupo->update(['activo' => true]));

            return back()->with('exito', "El grupo \"{$grupo->nombre}\" fue activado.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_grupo' => 'No fue posible activar el grupo.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | desactivar
    |----------------------------------------------------------------------
    */
    public function desactivar(Grupo $grupo)
    {
        try {

            DB::transaction(fn () => $grupo->update(['activo' => false]));

            return back()->with('exito', "El grupo \"{$grupo->nombre}\" fue desactivado.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_grupo' => 'No fue posible desactivar el grupo.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(Grupo $grupo)
    {
        // Pre-check: no eliminar si tiene inscripciones
        if ($grupo->inscripciones()->exists()) {
            return back()->withErrors([
                'error_grupo' =>
                    "No se puede eliminar el grupo \"{$grupo->nombre}\" porque tiene " .
                    $grupo->inscripciones()->count() . " inscripción(es).",
            ]);
        }

        // Pre-check: no eliminar si tiene asignaciones
        if ($grupo->asignaciones()->exists()) {
            return back()->withErrors([
                'error_grupo' =>
                    "No se puede eliminar el grupo \"{$grupo->nombre}\" porque tiene " .
                    $grupo->asignaciones()->count() . " asignación(es).",
            ]);
        }

        try {

            $nombre = $grupo->nombre;

            DB::transaction(fn () => $grupo->delete());

            return redirect()
                ->route('admin.academico.estructura.grupos.index')
                ->with('exito', "Grupo \"{$nombre}\" eliminado correctamente.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_grupo' => $e->getMessage()]);
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
            'grado_id'        => ['required', 'exists:grados,id'],
            'anio_lectivo_id' => ['required', 'exists:anios_lectivos,id'],
            'nombre'          => [
                'required', 'string', 'max:10',
                Rule::unique('grupos')->where(
                    fn ($q) => $q
                        ->where('grado_id', request('grado_id'))
                        ->where('anio_lectivo_id', request('anio_lectivo_id'))
                ),
            ],
            'cupo_maximo' => ['nullable', 'integer', 'min:1'],
            'activo'      => ['nullable', 'boolean'],
        ];
    }

    /*
    |----------------------------------------------------------------------
    | Reglas — update
    |----------------------------------------------------------------------
    */
    private function reglasUpdate(Grupo $grupo): array
    {
        return [
            'grado_id'        => ['required', 'exists:grados,id'],
            'anio_lectivo_id' => ['required', 'exists:anios_lectivos,id'],
            'nombre'          => [
                'required', 'string', 'max:10',
                Rule::unique('grupos')
                    ->where(
                        fn ($q) => $q
                            ->where('grado_id', request('grado_id'))
                            ->where('anio_lectivo_id', request('anio_lectivo_id'))
                    )
                    ->ignore($grupo->id),
            ],
            'cupo_maximo' => ['nullable', 'integer', 'min:1'],
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
            'grado_id.required'        => 'Debe seleccionar un grado.',
            'grado_id.exists'          => 'El grado seleccionado no es válido.',
            'anio_lectivo_id.required' => 'Debe seleccionar un año lectivo.',
            'anio_lectivo_id.exists'   => 'El año lectivo seleccionado no es válido.',
            'nombre.required'          => 'El identificador del grupo es obligatorio.',
            'nombre.max'               => 'El identificador no puede superar 10 caracteres.',
            'nombre.unique'            => 'Ya existe ese grupo para el grado y año seleccionado.',
            'cupo_maximo.integer'      => 'El cupo máximo debe ser un número.',
            'cupo_maximo.min'          => 'El cupo mínimo es 1.',
        ];
    }
}
