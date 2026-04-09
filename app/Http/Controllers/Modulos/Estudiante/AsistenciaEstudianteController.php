<?php

namespace App\Http\Controllers\Modulos\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\AnioLectivo;
use App\Models\Inscripcion;
use App\Models\Periodo;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| AsistenciaEstudianteController
|--------------------------------------------------------------------------
| Permite al estudiante consultar sus registros de asistencia (faltas).
| Solo lectura. Muestra faltas por materia agrupadas por periodo.
|
| Rutas:
|   GET /estudiante/asistencia          → estudiante.asistencia.index
|   GET /estudiante/asistencia/{periodo} → estudiante.asistencia.periodo
|--------------------------------------------------------------------------
*/

class AsistenciaEstudianteController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    | Muestra el resumen de asistencia del año activo agrupado por materia.
    | Incluye totales por periodo y total anual.
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $anioActivo = AnioLectivo::where('activo', true)->first();

        $inscripcion = $anioActivo
            ? Inscripcion::with([
                'grupo.anioLectivo',
                'inscripcionMaterias.asignacion.materia',
                'inscripcionMaterias.asistencias.periodo',
              ])
              ->where('estudiante_id', Auth::id())
              ->where('estado', 'activa')
              ->whereHas('grupo', fn ($q) =>
                  $q->where('anio_lectivo_id', $anioActivo->id)
              )
              ->first()
            : null;

        $periodos = $anioActivo
            ? Periodo::where('anio_lectivo_id', $anioActivo->id)
                ->orderBy('numero')
                ->get()
            : collect();

        return view('modulos.estudiante.asistencia.index', compact(
            'anioActivo',
            'inscripcion',
            'periodos'
        ));
    }

    /*
    |----------------------------------------------------------------------
    | porPeriodo
    |----------------------------------------------------------------------
    | Muestra las faltas del estudiante filtradas por un periodo específico.
    |----------------------------------------------------------------------
    */
    public function porPeriodo(Periodo $periodo)
    {
        $anioActivo = AnioLectivo::where('activo', true)->first();

        abort_unless(
            $anioActivo && $periodo->anio_lectivo_id === $anioActivo->id,
            404
        );

        $inscripcion = Inscripcion::with([
                'grupo',
                'inscripcionMaterias.asignacion.materia',
                'inscripcionMaterias.asistencias' => fn ($q) =>
                    $q->where('periodo_id', $periodo->id),
            ])
            ->where('estudiante_id', Auth::id())
            ->where('estado', 'activa')
            ->whereHas('grupo', fn ($q) =>
                $q->where('anio_lectivo_id', $anioActivo->id)
            )
            ->first();

        $periodos = Periodo::where('anio_lectivo_id', $anioActivo->id)
            ->orderBy('numero')
            ->get();

        return view('modulos.estudiante.asistencia.periodo', compact(
            'periodo',
            'inscripcion',
            'periodos',
            'anioActivo'
        ));
    }
}
