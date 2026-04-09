<?php

namespace App\Http\Controllers\Modulos\Docente;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Asignacion;
use App\Models\InscripcionMateria;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/*
|--------------------------------------------------------------------------
| AsistenciaDocenteController
|--------------------------------------------------------------------------
| Permite al docente registrar, editar y eliminar las faltas
| de sus estudiantes por periodo y por materia asignada.
|
| Flujo de navegación:
|   1. index()        → mis asignaciones (elegir dónde registrar)
|   2. estudiantes()  → estudiantes del grupo con sus faltas por periodo
|   3. create()       → formulario para registrar faltas de un estudiante
|   4. store()        → persiste la asistencia
|   5. edit()         → formulario para editar asistencia existente
|   6. update()       → actualiza el registro
|   7. destroy()      → elimina el registro (solo periodo abierto)
|
| Rutas:
|   GET  /docente/asistencia                                          → docente.asistencia.index
|   GET  /docente/asistencia/{asignacion}/estudiantes                 → docente.asistencia.estudiantes
|   GET  /docente/asistencia/{asignacion}/{inscripcionMateria}/create → docente.asistencia.create
|   POST /docente/asistencia/{asignacion}/{inscripcionMateria}        → docente.asistencia.store
|   GET  /docente/asistencia/{asistencia}/edit                       → docente.asistencia.edit
|   PUT  /docente/asistencia/{asistencia}                            → docente.asistencia.update
|   DEL  /docente/asistencia/{asistencia}                            → docente.asistencia.destroy
|--------------------------------------------------------------------------
*/

class AsistenciaDocenteController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    | Lista las asignaciones activas del docente para elegir
    | en qué grupo/materia registrar asistencias.
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

        return view('modulos.docente.asistencia.index', compact('asignaciones'));
    }

    /*
    |----------------------------------------------------------------------
    | estudiantes
    |----------------------------------------------------------------------
    | Lista los estudiantes inscritos en la asignación con sus registros
    | de asistencia por periodo. Tabla similar a la de notas.
    |----------------------------------------------------------------------
    */
    public function estudiantes(Asignacion $asignacion)
    {
        abort_unless($asignacion->docente_id === Auth::id(), 403);

        // Inscripciones de materia activas con sus asistencias y notas
        $inscripcionMaterias = InscripcionMateria::with([
                'inscripcion.estudiante',
                'asistencias.periodo',
            ])
            ->where('asignacion_id', $asignacion->id)
            ->where('estado', 'activa')
            ->get();

        // Periodos del año lectivo para las columnas
        $periodos = Periodo::where('anio_lectivo_id', $asignacion->grupo->anio_lectivo_id)
            ->orderBy('numero')
            ->get();

        return view('modulos.docente.asistencia.estudiantes', compact(
            'asignacion',
            'inscripcionMaterias',
            'periodos'
        ));
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    | Formulario para registrar las faltas de un estudiante en un periodo.
    |----------------------------------------------------------------------
    */
    public function create(Asignacion $asignacion, InscripcionMateria $inscripcionMateria)
    {
        abort_unless($asignacion->docente_id === Auth::id(), 403);

        $periodos = Periodo::where('anio_lectivo_id', $asignacion->grupo->anio_lectivo_id)
            ->orderBy('numero')
            ->get();

        // Periodos que ya tienen registro de asistencia para este estudiante
        $periodosConRegistro = $inscripcionMateria
            ->asistencias()
            ->pluck('periodo_id');

        return view('modulos.docente.asistencia.create', compact(
            'asignacion',
            'inscripcionMateria',
            'periodos',
            'periodosConRegistro'
        ));
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    | Persiste el registro de asistencia. El modelo valida periodo abierto
    | e inscripción activa.
    |----------------------------------------------------------------------
    */
    public function store(
        Request $request,
        Asignacion $asignacion,
        InscripcionMateria $inscripcionMateria
    ) {
        abort_unless($asignacion->docente_id === Auth::id(), 403);

        $validated = $request->validate([
            'periodo_id'            => ['required', 'exists:periodos,id'],
            'faltas_justificadas'   => ['required', 'integer', 'min:0', 'max:999'],
            'faltas_injustificadas' => ['required', 'integer', 'min:0', 'max:999'],
            'observacion'           => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['inscripcion_materia_id'] = $inscripcionMateria->id;

        try {
            DB::transaction(function () use ($validated) {

                $existe = Asistencia::where('inscripcion_materia_id', $validated['inscripcion_materia_id'])
                    ->where('periodo_id', $validated['periodo_id'])
                    ->exists();

                if ($existe) {
                    throw new RuntimeException(
                        'Ya existe un registro de asistencia para este periodo.'
                    );
                }

                Asistencia::create($validated);
            });

        } catch (RuntimeException $e) {
            return back()
                ->withErrors(['error_academico' => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('docente.asistencia.estudiantes', $asignacion)
            ->with('exito', 'Asistencia registrada correctamente.');
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    | Formulario para editar un registro de asistencia existente.
    |----------------------------------------------------------------------
    */
    public function edit(Asistencia $asistencia)
    {
        $asistencia->load(
            'inscripcionMateria.asignacion',
            'inscripcionMateria.inscripcion.estudiante',
            'periodo'
        );

        abort_unless(
            $asistencia->inscripcionMateria->asignacion->docente_id === Auth::id(),
            403
        );

        return view('modulos.docente.asistencia.edit', compact('asistencia'));
    }

    /*
    |----------------------------------------------------------------------
    | update
    |----------------------------------------------------------------------
    | Actualiza el registro de asistencia.
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Asistencia $asistencia)
    {
        $asistencia->load('inscripcionMateria.asignacion');

        abort_unless(
            $asistencia->inscripcionMateria->asignacion->docente_id === Auth::id(),
            403
        );

        $validated = $request->validate([
            'faltas_justificadas'   => ['required', 'integer', 'min:0', 'max:999'],
            'faltas_injustificadas' => ['required', 'integer', 'min:0', 'max:999'],
            'observacion'           => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::transaction(fn () => $asistencia->update($validated));

        } catch (RuntimeException $e) {
            return back()
                ->withErrors(['error_academico' => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('docente.asistencia.estudiantes', $asistencia->inscripcionMateria->asignacion)
            ->with('exito', 'Asistencia actualizada correctamente.');
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    | Elimina el registro. Solo permitido si el periodo está abierto.
    |----------------------------------------------------------------------
    */
    public function destroy(Asistencia $asistencia)
    {
        $asistencia->load('inscripcionMateria.asignacion');

        abort_unless(
            $asistencia->inscripcionMateria->asignacion->docente_id === Auth::id(),
            403
        );

        $asignacion = $asistencia->inscripcionMateria->asignacion;

        try {
            DB::transaction(fn () => $asistencia->delete());

        } catch (RuntimeException $e) {
            return back()->withErrors(['error_academico' => $e->getMessage()]);
        }

        return redirect()
            ->route('docente.asistencia.estudiantes', $asignacion)
            ->with('exito', 'Registro de asistencia eliminado.');
    }
}
