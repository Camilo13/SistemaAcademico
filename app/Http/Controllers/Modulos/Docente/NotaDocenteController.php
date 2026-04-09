<?php

namespace App\Http\Controllers\Modulos\Docente;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\InscripcionMateria;
use App\Models\Nota;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class NotaDocenteController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index — Lista mis asignaciones activas
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $asignaciones = Asignacion::with([
                'materia',
                'grupo.grado',
                'grupo.anioLectivo',
            ])
            ->where('docente_id', Auth::id())
            ->where('activa', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('modulos.docente.notas.index', compact('asignaciones'));
    }

    /*
    |----------------------------------------------------------------------
    | estudiantes — Lista estudiantes inscritos con sus notas por periodo
    |----------------------------------------------------------------------
    */
    public function estudiantes(Asignacion $asignacion)
    {
        abort_unless($asignacion->docente_id === Auth::id(), 403);

        $inscripcionMaterias = InscripcionMateria::with([
                'inscripcion.estudiante',
                'notas.periodo',
            ])
            ->where('asignacion_id', $asignacion->id)
            ->where('estado', 'activa')
            ->get();

        $periodos = Periodo::where('anio_lectivo_id', $asignacion->grupo->anio_lectivo_id)
            ->orderBy('numero')
            ->get();

        return view('modulos.docente.notas.estudiantes', compact(
            'asignacion',
            'inscripcionMaterias',
            'periodos'
        ));
    }

    /*
    |----------------------------------------------------------------------
    | create — Formulario para registrar nota a un estudiante
    |----------------------------------------------------------------------
    */
    public function create(Asignacion $asignacion, InscripcionMateria $inscripcionMateria)
    {
        abort_unless($asignacion->docente_id === Auth::id(), 403);

        $periodos = Periodo::where('anio_lectivo_id', $asignacion->grupo->anio_lectivo_id)
            ->orderBy('numero')
            ->get();

        $notasExistentes = $inscripcionMateria->notas()->pluck('periodo_id');

        return view('modulos.docente.notas.create', compact(
            'asignacion',
            'inscripcionMateria',
            'periodos',
            'notasExistentes'
        ));
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(
        Request $request,
        Asignacion $asignacion,
        InscripcionMateria $inscripcionMateria
    ) {
        abort_unless($asignacion->docente_id === Auth::id(), 403);

        $validated = $request->validate([
            'periodo_id'  => ['required', 'exists:periodos,id'],
            'nota'        => ['required', 'numeric', 'min:0', 'max:5'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ], [
            'periodo_id.required' => 'Debe seleccionar un periodo.',
            'nota.required'       => 'La nota es obligatoria.',
            'nota.numeric'        => 'La nota debe ser un número.',
            'nota.min'            => 'La nota mínima es 0.00.',
            'nota.max'            => 'La nota máxima es 5.00.',
        ]);

        $validated['inscripcion_materia_id'] = $inscripcionMateria->id;

        try {

            DB::transaction(function () use ($validated) {

                $existe = Nota::where('inscripcion_materia_id', $validated['inscripcion_materia_id'])
                    ->where('periodo_id', $validated['periodo_id'])
                    ->exists();

                if ($existe) {
                    throw new RuntimeException('Ya existe una nota registrada para este periodo.');
                }

                Nota::create($validated);
            });

        } catch (RuntimeException $e) {
            return back()
                ->withErrors(['error_academico' => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('docente.notas.estudiantes', $asignacion)
            ->with('exito', 'Nota registrada correctamente.');
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(Nota $nota)
    {
        $nota->load('inscripcionMateria.asignacion', 'periodo');

        abort_unless(
            $nota->inscripcionMateria->asignacion->docente_id === Auth::id(),
            403
        );

        return view('modulos.docente.notas.edit', compact('nota'));
    }

    /*
    |----------------------------------------------------------------------
    | update
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Nota $nota)
    {
        $nota->load('inscripcionMateria.asignacion');

        abort_unless(
            $nota->inscripcionMateria->asignacion->docente_id === Auth::id(),
            403
        );

        $validated = $request->validate([
            'nota'        => ['required', 'numeric', 'min:0', 'max:5'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ], [
            'nota.required' => 'La nota es obligatoria.',
            'nota.numeric'  => 'La nota debe ser un número.',
            'nota.min'      => 'La nota mínima es 0.00.',
            'nota.max'      => 'La nota máxima es 5.00.',
        ]);

        try {
            DB::transaction(fn () => $nota->update($validated));
        } catch (RuntimeException $e) {
            return back()
                ->withErrors(['error_academico' => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('docente.notas.estudiantes', $nota->inscripcionMateria->asignacion)
            ->with('exito', 'Nota actualizada correctamente.');
    }

    /*
    |----------------------------------------------------------------------
    | destroy — Elimina una nota individual
    |----------------------------------------------------------------------
    */
    public function destroy(Nota $nota)
    {
        $nota->load('inscripcionMateria.asignacion');

        abort_unless(
            $nota->inscripcionMateria->asignacion->docente_id === Auth::id(),
            403
        );

        $asignacion = $nota->inscripcionMateria->asignacion;

        try {
            DB::transaction(fn () => $nota->delete());
        } catch (RuntimeException $e) {
            return back()->withErrors(['error_academico' => $e->getMessage()]);
        }

        return redirect()
            ->route('docente.notas.estudiantes', $asignacion)
            ->with('exito', 'Nota eliminada correctamente.');
    }

    /*
    |----------------------------------------------------------------------
    | eliminarNotasEstudiante
    |----------------------------------------------------------------------
    | Elimina TODAS las notas de un estudiante en esta asignación.
    | Usado por la barra bulk desde la vista de estudiantes.
    | Ruta: DELETE /docente/notas/{asignacion}/{inscripcionMateria}/borrar-notas
    |----------------------------------------------------------------------
    */
    public function eliminarNotasEstudiante(
        Asignacion $asignacion,
        InscripcionMateria $inscripcionMateria
    ) {
        abort_unless($asignacion->docente_id === Auth::id(), 403);

        // Seguridad: la inscripcionMateria debe pertenecer a esta asignación
        abort_unless($inscripcionMateria->asignacion_id === $asignacion->id, 403);

        // Pre-check: solo si tiene notas en periodos abiertos
        $notasCerradas = $inscripcionMateria->notas()
            ->whereHas('periodo', fn ($q) => $q->where('abierto', false))
            ->exists();

        if ($notasCerradas) {
            return back()->withErrors([
                'error_academico' =>
                    'No se pueden eliminar notas de periodos cerrados para ' .
                    optional($inscripcionMateria->inscripcion->estudiante)->nombre_completo . '.',
            ]);
        }

        try {

            $nombre = optional($inscripcionMateria->inscripcion->estudiante)->nombre_completo;

            DB::transaction(fn () => $inscripcionMateria->notas()->delete());

            return redirect()
                ->route('docente.notas.estudiantes', $asignacion)
                ->with('exito', "Notas de \"{$nombre}\" eliminadas correctamente.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_academico' => $e->getMessage()]);
        }
    }
}
