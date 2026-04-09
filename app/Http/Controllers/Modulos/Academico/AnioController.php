<?php

namespace App\Http\Controllers\Modulos\Academico;

use App\Http\Controllers\Controller;
use App\Models\AnioLectivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class AnioController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | index
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $anios = AnioLectivo::orderByDesc('fecha_inicio')->get();

        return view('modulos.academico.anio.index', compact('anios'));
    }

    /*
    |--------------------------------------------------------------------------
    | create
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('modulos.academico.anio.create');
    }

    /*
    |--------------------------------------------------------------------------
    | store
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        // Limpiar antes de validar
        $request->merge([
            'nombre' => trim($request->nombre ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglas(),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($request, $validated) {
                AnioLectivo::create([
                    'nombre'       => $validated['nombre'],
                    'fecha_inicio' => $validated['fecha_inicio'],
                    'fecha_fin'    => $validated['fecha_fin'],
                    'activo'       => $request->boolean('activo', false),
                ]);
            });

            return redirect()
                ->route('admin.academico.anios.index')
                ->with('exito', 'Año lectivo creado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_academico' => $e->getMessage()])
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | edit
    |--------------------------------------------------------------------------
    */
    public function edit(AnioLectivo $anio)
    {
        return view('modulos.academico.anio.edit', compact('anio'));
    }

    /*
    |--------------------------------------------------------------------------
    | update
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, AnioLectivo $anio)
    {
        $request->merge([
            'nombre' => trim($request->nombre ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglas($anio->id),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($anio, $validated) {
                $anio->update([
                    'nombre'       => $validated['nombre'],
                    'fecha_inicio' => $validated['fecha_inicio'],
                    'fecha_fin'    => $validated['fecha_fin'],
                ]);
            });

            return redirect()
                ->route('admin.academico.anios.index')
                ->with('exito', 'Año lectivo actualizado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_academico' => $e->getMessage()])
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | activar
    |--------------------------------------------------------------------------
    | No se desactiva manualmente — solo se activa otro año.
    | El modelo garantiza unicidad del año activo via boot().
    */
    public function activar(AnioLectivo $anio)
    {
        // Pre-check: no activar si la fecha de fin ya pasó
        if ($anio->fecha_fin->isPast()) {
            return back()->withErrors([
                'error_academico' =>
                    'No se puede activar el año lectivo "' . $anio->nombre . '" ' .
                    'porque su fecha de finalización (' .
                    $anio->fecha_fin->format('d/m/Y') .
                    ') ya ha pasado. Edita las fechas antes de activarlo.',
            ]);
        }

        try {

            DB::transaction(function () use ($anio) {
                $anio->update(['activo' => true]);
            });

            return back()->with('exito', 'El año lectivo fue activado correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_academico' => $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | destroy
    |--------------------------------------------------------------------------
    */
    public function destroy(AnioLectivo $anio)
    {
        // Pre-check 1: no eliminar si está activo
        if ($anio->activo) {
            return back()->withErrors([
                'error_academico' =>
                    'No se puede eliminar el año lectivo "' . $anio->nombre . '" ' .
                    'porque está activo. Activa otro año primero.',
            ]);
        }

        // Pre-check 2: no eliminar si tiene periodos
        if ($anio->periodos()->exists()) {
            return back()->withErrors([
                'error_academico' =>
                    'No se puede eliminar "' . $anio->nombre . '" porque tiene ' .
                    $anio->periodos()->count() . ' periodo(s). Elimínalos primero.',
            ]);
        }

        // Pre-check 3: no eliminar si tiene grupos
        if ($anio->grupos()->exists()) {
            return back()->withErrors([
                'error_academico' =>
                    'No se puede eliminar "' . $anio->nombre . '" porque tiene grupos asociados.',
            ]);
        }

        try {

            $nombre = $anio->nombre;

            DB::transaction(fn () => $anio->delete());

            return redirect()
                ->route('admin.academico.anios.index')
                ->with('exito', 'Año lectivo "' . $nombre . '" eliminado correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_academico' => $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reglas de validación
    |--------------------------------------------------------------------------
    */
    private function reglas($id = null): array
    {
        return [
            'nombre' => [
                'required', 'string', 'max:20',
                Rule::unique('anios_lectivos', 'nombre')->ignore($id),
            ],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after_or_equal:fecha_inicio'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Mensajes personalizados
    |--------------------------------------------------------------------------
    */
    private function mensajes(): array
    {
        return [
            'nombre.required'          => 'El nombre del año lectivo es obligatorio.',
            'nombre.max'               => 'El nombre no puede superar los 20 caracteres.',
            'nombre.unique'            => 'Ya existe un año lectivo con ese nombre.',
            'fecha_inicio.required'    => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required'       => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
        ];
    }
}
