<?php

namespace App\Http\Controllers\Modulos\Academico\Estructura;

use App\Http\Controllers\Controller;
use App\Models\Grado;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class GradoController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Grado::with('sede');

        if ($request->filled('sede')) {
            $query->where('sede_id', $request->sede);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $grados = $query
            ->orderBy('nivel')
            ->paginate(15)
            ->withQueryString();

        $sedes = Sede::orderBy('nombre')->get();

        return view(
            'modulos.academico.estructura.grado.index',
            compact('grados', 'sedes')
        );
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create()
    {
        $sedes = Sede::orderBy('nombre')->get();

        return view(
            'modulos.academico.estructura.grado.create',
            compact('sedes')
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
            'nombre' => trim($request->nombre ?? ''),
            'tipo'   => trim($request->tipo   ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglasStore(),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($request, $validated) {
                Grado::create([
                    'sede_id' => $validated['sede_id'],
                    'nombre'  => $validated['nombre'],
                    'nivel'   => $validated['nivel'],
                    'tipo'    => $validated['tipo'],
                    'activo'  => $request->boolean('activo', true),
                ]);
            });

            return redirect()
                ->route('admin.academico.grados.index')
                ->with('exito', 'Grado creado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_grado' => 'Ocurrió un error al crear el grado.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(Grado $grado)
    {
        $sedes = Sede::orderBy('nombre')->get();

        return view(
            'modulos.academico.estructura.grado.edit',
            compact('grado', 'sedes')
        );
    }

    /*
    |----------------------------------------------------------------------
    | update — Solo datos (sede, nombre, nivel, tipo)
    | La visibilidad se maneja con activar/desactivar
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Grado $grado)
    {
        $request->merge([
            'nombre' => trim($request->nombre ?? ''),
            'tipo'   => trim($request->tipo   ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglasUpdate($grado),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($grado, $validated) {
                $grado->update([
                    'sede_id' => $validated['sede_id'],
                    'nombre'  => $validated['nombre'],
                    'nivel'   => $validated['nivel'],
                    'tipo'    => $validated['tipo'],
                ]);
            });

            return redirect()
                ->route('admin.academico.grados.index')
                ->with('exito', 'Grado actualizado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_grado' => 'Ocurrió un error al actualizar el grado.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | activar
    |----------------------------------------------------------------------
    */
    public function activar(Grado $grado)
    {
        try {

            DB::transaction(fn () => $grado->update(['activo' => true]));

            return back()->with('exito', "El grado \"{$grado->nombre}\" fue activado.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_grado' => 'No fue posible activar el grado.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | desactivar
    |----------------------------------------------------------------------
    */
    public function desactivar(Grado $grado)
    {
        try {

            DB::transaction(fn () => $grado->update(['activo' => false]));

            return back()->with('exito', "El grado \"{$grado->nombre}\" fue desactivado.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_grado' => 'No fue posible desactivar el grado.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(Grado $grado)
    {
        // Pre-check: no eliminar si tiene grupos
        if ($grado->grupos()->exists()) {
            return back()->withErrors([
                'error_grado' =>
                    "No se puede eliminar el grado \"{$grado->nombre}\" porque tiene " .
                    $grado->grupos()->count() . " grupo(s) asociado(s).",
            ]);
        }

        // Pre-check: no eliminar si tiene materias
        if ($grado->materias()->exists()) {
            return back()->withErrors([
                'error_grado' =>
                    "No se puede eliminar el grado \"{$grado->nombre}\" porque tiene " .
                    $grado->materias()->count() . " materia(s) asociada(s).",
            ]);
        }

        try {

            $nombre = $grado->nombre;

            DB::transaction(fn () => $grado->delete());

            return redirect()
                ->route('admin.academico.grados.index')
                ->with('exito', "Grado \"{$nombre}\" eliminado correctamente.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_grado' => $e->getMessage()]);
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
            'sede_id' => ['required', 'exists:sedes,id'],
            'nombre'  => ['required', 'string', 'max:100'],
            'nivel'   => [
                'required', 'integer', 'min:1', 'max:11',
                Rule::unique('grados')->where(
                    fn ($q) => $q->where('sede_id', request('sede_id'))
                ),
            ],
            'tipo'   => ['required', 'string', 'max:50'],
            'activo' => ['nullable', 'boolean'],
        ];
    }

    /*
    |----------------------------------------------------------------------
    | Reglas — update
    |----------------------------------------------------------------------
    */
    private function reglasUpdate(Grado $grado): array
    {
        return [
            'sede_id' => ['required', 'exists:sedes,id'],
            'nombre'  => ['required', 'string', 'max:100'],
            'nivel'   => [
                'required', 'integer', 'min:1', 'max:11',
                Rule::unique('grados')
                    ->where(fn ($q) => $q->where('sede_id', request('sede_id')))
                    ->ignore($grado->id),
            ],
            'tipo' => ['required', 'string', 'max:50'],
        ];
    }

    /*
    |----------------------------------------------------------------------
    | Mensajes personalizados
    |----------------------------------------------------------------------
    */
    private function mensajes(): array
    {
        return [
            'sede_id.required' => 'Debe seleccionar una sede.',
            'sede_id.exists'   => 'La sede seleccionada no es válida.',
            'nombre.required'  => 'El nombre del grado es obligatorio.',
            'nombre.max'       => 'El nombre no puede superar los 100 caracteres.',
            'nivel.required'   => 'Debe indicar el nivel del grado.',
            'nivel.integer'    => 'El nivel debe ser un número.',
            'nivel.min'        => 'El nivel mínimo es 1.',
            'nivel.max'        => 'El nivel máximo es 11.',
            'nivel.unique'     => 'Ese nivel ya existe en la sede seleccionada.',
            'tipo.required'    => 'Debe indicar el tipo de grado.',
        ];
    }
}
