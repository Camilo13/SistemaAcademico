<?php

namespace App\Http\Controllers\Modulos\Academico;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\AnioLectivo;
use App\Models\Grupo;
use App\Models\Horario;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| HorarioController  (Admin)
|--------------------------------------------------------------------------
| Gestión completa de horarios por grupo.
|
| Flujo:
|   1. Admin selecciona un grupo → ve la cuadrícula semanal vacía/cargada
|   2. Asigna franjas: elige asignación + día + bloque
|   3. El sistema valida que no haya choques de grupo ni de docente
|
| Rutas:
|   GET  /admin/horarios                         → admin.horarios.index
|   GET  /admin/horarios/grupo/{grupo}           → admin.horarios.grupo
|   GET  /admin/horarios/create/{grupo}          → admin.horarios.create
|   POST /admin/horarios                         → admin.horarios.store
|   DELETE /admin/horarios/{horario}             → admin.horarios.destroy
|--------------------------------------------------------------------------
*/

class HorarioController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    | Selector de grupo para ver/editar su horario.
    |----------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $anios = AnioLectivo::orderByDesc('nombre')->get();

        // Sin filtro → todos los grupos activos de todos los años
        // Con filtro → solo los grupos del año seleccionado
        if ($request->filled('anio')) {
            $anioSeleccionado = AnioLectivo::find($request->anio);
            $grupos = Grupo::with(['grado'])
                ->where('anio_lectivo_id', $request->anio)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();
        } else {
            $anioSeleccionado = null;
            $grupos = Grupo::with(['grado', 'anioLectivo'])
                ->where('activo', true)
                ->orderByDesc('anio_lectivo_id')
                ->orderBy('nombre')
                ->get();
        }

        return view('modulos.academico.horario.index', compact(
            'anios', 'grupos', 'anioSeleccionado'
        ));
    }

    /*
    |----------------------------------------------------------------------
    | grupo
    |----------------------------------------------------------------------
    | Muestra la cuadrícula semanal completa del grupo.
    |----------------------------------------------------------------------
    */
    public function grupo(Grupo $grupo)
    {
        $grupo->load(['grado', 'anioLectivo']);

        // Todas las asignaciones activas del grupo con sus horarios cargados
        $asignaciones = Asignacion::with(['docente', 'materia', 'horarios'])
            ->where('grupo_id', $grupo->id)
            ->where('activa', true)
            ->get();

        // Construir mapa: dia → bloque → horario (para la cuadrícula)
        $cuadricula = [];
        foreach (Horario::DIAS as $dia) {
            foreach (array_keys(Horario::BLOQUES) as $bloque) {
                $cuadricula[$dia][$bloque] = null;
            }
        }

        foreach ($asignaciones as $asignacion) {
            foreach ($asignacion->horarios as $horario) {
                $cuadricula[$horario->dia_semana][$horario->bloque] = [
                    'horario'    => $horario,
                    'asignacion' => $asignacion,
                ];
            }
        }

        return view('modulos.academico.horario.grupo', compact(
            'grupo', 'asignaciones', 'cuadricula'
        ));
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    | Formulario para agregar una franja al horario de un grupo.
    |----------------------------------------------------------------------
    */
    public function create(Grupo $grupo)
    {
        $grupo->load(['grado', 'anioLectivo']);

        $asignaciones = Asignacion::with(['docente', 'materia'])
            ->where('grupo_id', $grupo->id)
            ->where('activa', true)
            ->get();

        // Celdas ya ocupadas en este grupo (para deshabilitar en el formulario)
        $ocupadas = Horario::whereIn('asignacion_id', $asignaciones->pluck('id'))
            ->get(['dia_semana', 'bloque'])
            ->map(fn($h) => "{$h->dia_semana}_{$h->bloque}")
            ->toArray();

        return view('modulos.academico.horario.create', compact(
            'grupo', 'asignaciones', 'ocupadas'
        ));
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'grupo_id'      => ['required', 'exists:grupos,id'],
            'asignacion_id' => ['required', 'exists:asignaciones,id'],
            'dia_semana'    => ['required', 'in:lunes,martes,miercoles,jueves,viernes'],
            'bloque'        => ['required', 'integer', 'between:1,6'],
        ]);

        $asignacion = Asignacion::with('grupo')->findOrFail($validated['asignacion_id']);
        $grupo      = $asignacion->grupo;

        // ── Validar: choque de grupo (mismo grupo, mismo día, mismo bloque) ──
        $choqueGrupo = Horario::whereHas('asignacion', fn($q) =>
                $q->where('grupo_id', $grupo->id)
            )
            ->where('dia_semana', $validated['dia_semana'])
            ->where('bloque',     $validated['bloque'])
            ->exists();

        if ($choqueGrupo) {
            return back()
                ->withInput()
                ->withErrors(['choque' => 'Ese bloque ya está ocupado para este grupo en ese día.']);
        }

        // ── Validar: choque de docente (mismo docente, mismo día, mismo bloque en otro grupo) ──
        $choqueDocente = Horario::whereHas('asignacion', fn($q) =>
                $q->where('docente_id', $asignacion->docente_id)
            )
            ->where('dia_semana', $validated['dia_semana'])
            ->where('bloque',     $validated['bloque'])
            ->exists();

        if ($choqueDocente) {
            return back()
                ->withInput()
                ->withErrors(['choque' => 'El docente ya tiene clase en ese bloque y día (otro grupo).']);
        }

        Horario::create([
            'asignacion_id' => $validated['asignacion_id'],
            'dia_semana'    => $validated['dia_semana'],
            'bloque'        => $validated['bloque'],
        ]);

        return redirect()
            ->route('admin.academico.horarios.grupo', $validated['grupo_id'])
            ->with('exito', 'Franja horaria agregada correctamente.');
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(Horario $horario)
    {
        $grupoId = $horario->asignacion->grupo_id;
        $horario->delete();

        return redirect()
            ->route('admin.academico.horarios.grupo', $grupoId)
            ->with('exito', 'Franja eliminada correctamente.');
    }
}