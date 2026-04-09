<?php

namespace App\Http\Controllers\Modulos\Academico;

use App\Http\Controllers\Controller;
use App\Models\AnioLectivo;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/*
|--------------------------------------------------------------------------
| PeriodoController — Periodos académicos (admin)
|--------------------------------------------------------------------------
| Mejoras respecto a la versión anterior:
|   - trim() en nombre antes de validar
|   - nombre agregado a las reglas de validación
|   - destroy() redirige al index (no back()) tras eliminar
|   - Pre-checks explícitos en destroy
|--------------------------------------------------------------------------
*/

class PeriodoController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index(AnioLectivo $anioLectivo)
    {
        $periodos = $anioLectivo->periodos()
            ->orderBy('numero')
            ->get();

        return view(
            'modulos.academico.periodo.index',
            compact('anioLectivo', 'periodos')
        );
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create(AnioLectivo $anioLectivo)
    {
        if (!$anioLectivo->activo) {
            return back()->withErrors([
                'error_academico' => 'Solo se pueden crear periodos en un año lectivo activo.',
            ]);
        }

        return view(
            'modulos.academico.periodo.create',
            compact('anioLectivo')
        );
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(Request $request, AnioLectivo $anioLectivo)
    {
        if (!$anioLectivo->activo) {
            return back()->withErrors([
                'error_academico' => 'No se pueden registrar periodos en un año lectivo inactivo.',
            ]);
        }

        $request->merge([
            'nombre' => trim($request->nombre ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglasStore(),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($anioLectivo, $validated) {
                $anioLectivo->periodos()->create([
                    'numero'       => $validated['numero'],
                    'nombre'       => $validated['nombre'] ?: null,
                    'fecha_inicio' => $validated['fecha_inicio'],
                    'fecha_fin'    => $validated['fecha_fin'],
                ]);
            });

            return redirect()
                ->route('admin.academico.anios.periodos.index', $anioLectivo->id)
                ->with('exito', 'Periodo creado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_academico' => $e->getMessage()])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(AnioLectivo $anioLectivo, Periodo $periodo)
    {
        if ($periodo->anio_lectivo_id !== $anioLectivo->id) {
            return back()->withErrors(['error_academico' => 'Operación no permitida.']);
        }

        if ($periodo->estaCerrado()) {
            return back()->withErrors([
                'error_academico' => 'No se puede editar un periodo que está cerrado.',
            ]);
        }

        return view(
            'modulos.academico.periodo.edit',
            compact('anioLectivo', 'periodo')
        );
    }

    /*
    |----------------------------------------------------------------------
    | update
    |----------------------------------------------------------------------
    */
    public function update(Request $request, AnioLectivo $anioLectivo, Periodo $periodo)
    {
        if ($periodo->anio_lectivo_id !== $anioLectivo->id) {
            return back()->withErrors(['error_academico' => 'Operación no permitida.']);
        }

        if ($periodo->estaCerrado()) {
            return back()->withErrors([
                'error_academico' => 'No se puede modificar un periodo cerrado.',
            ]);
        }

        $request->merge([
            'nombre' => trim($request->nombre ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglasUpdate(),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($periodo, $validated) {
                $periodo->update([
                    'nombre'       => $validated['nombre'] ?: null,
                    'fecha_inicio' => $validated['fecha_inicio'],
                    'fecha_fin'    => $validated['fecha_fin'],
                ]);
            });

            return redirect()
                ->route('admin.academico.anios.periodos.index', $anioLectivo->id)
                ->with('exito', 'Periodo actualizado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_academico' => $e->getMessage()])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | cerrar
    |----------------------------------------------------------------------
    */
    public function cerrar(AnioLectivo $anioLectivo, Periodo $periodo)
    {
        if ($periodo->anio_lectivo_id !== $anioLectivo->id) {
            return back()->withErrors(['error_academico' => 'Operación no permitida.']);
        }

        if ($periodo->estaCerrado()) {
            return back()->withErrors(['error_academico' => 'El periodo ya está cerrado.']);
        }

        try {

            DB::transaction(fn () => $periodo->cerrar());

            return back()->with('exito', "El periodo \"{$periodo->nombre}\" fue cerrado.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_academico' => $e->getMessage()]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | reabrir
    |----------------------------------------------------------------------
    */
    public function reabrir(AnioLectivo $anioLectivo, Periodo $periodo)
    {
        if ($periodo->anio_lectivo_id !== $anioLectivo->id) {
            return back()->withErrors(['error_academico' => 'Operación no permitida.']);
        }

        if ($periodo->estaAbierto()) {
            return back()->withErrors(['error_academico' => 'El periodo ya está abierto.']);
        }

        try {

            DB::transaction(fn () => $periodo->abrir());

            return back()->with('exito', "El periodo \"{$periodo->nombre}\" fue reabierto.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_academico' => $e->getMessage()]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(AnioLectivo $anioLectivo, Periodo $periodo)
    {
        if ($periodo->anio_lectivo_id !== $anioLectivo->id) {
            return back()->withErrors(['error_academico' => 'Operación no permitida.']);
        }

        // Pre-check: no eliminar si tiene notas
        if ($periodo->notas()->exists()) {
            return back()->withErrors([
                'error_academico' =>
                    'No se puede eliminar el periodo "' . $periodo->nombre . '" ' .
                    'porque tiene ' . $periodo->notas()->count() . ' nota(s) registrada(s).',
            ]);
        }

        try {

            $nombre    = $periodo->nombre;
            $anioId    = $anioLectivo->id;

            DB::transaction(fn () => $periodo->delete());

            // Redirigir al index — nunca back() tras eliminar
            return redirect()
                ->route('admin.academico.anios.periodos.index', $anioId)
                ->with('exito', "Periodo \"{$nombre}\" eliminado correctamente.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_academico' => $e->getMessage()]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | Reglas de validación
    |----------------------------------------------------------------------
    */
    private function reglasStore(): array
    {
        return [
            'numero'       => ['required', 'integer', 'min:1', 'max:3'],
            'nombre'       => ['nullable', 'string', 'max:100'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after:fecha_inicio'],
        ];
    }

    private function reglasUpdate(): array
    {
        return [
            'nombre'       => ['nullable', 'string', 'max:100'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after:fecha_inicio'],
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
            'numero.required'       => 'Debe indicar el número del periodo.',
            'numero.integer'        => 'El número del periodo debe ser válido.',
            'numero.min'            => 'El número mínimo del periodo es 1.',
            'numero.max'            => 'El número máximo del periodo es 3.',
            'nombre.max'            => 'El nombre no puede superar los 100 caracteres.',
            'fecha_inicio.required' => 'Debe indicar la fecha de inicio.',
            'fecha_inicio.date'     => 'La fecha de inicio no es válida.',
            'fecha_fin.required'    => 'Debe indicar la fecha de finalización.',
            'fecha_fin.date'        => 'La fecha de finalización no es válida.',
            'fecha_fin.after'       => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ];
    }
}
