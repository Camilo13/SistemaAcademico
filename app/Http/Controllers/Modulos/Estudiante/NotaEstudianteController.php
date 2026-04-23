<?php

namespace App\Http\Controllers\Modulos\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\AnioLectivo;
use App\Models\Inscripcion;
use App\Models\Periodo;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| NotaEstudianteController
|--------------------------------------------------------------------------
| Permite al estudiante consultar sus notas. Solo lectura.
|
| index     → todas las notas del año activo agrupadas por materia,
|             con ficha de contexto (año, grado, grupo, sede, materias).
| porPeriodo → notas filtradas por un periodo específico.
|
| Rutas:
|   GET /estudiante/notas            →  estudiante.notas.index
|   GET /estudiante/notas/{periodo}  →  estudiante.notas.periodo
|--------------------------------------------------------------------------
*/

class NotaEstudianteController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    | Muestra todas las notas del año activo agrupadas por materia.
    | Incluye ficha de contexto con año, grupo, grado y sede.
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $anioActivo = AnioLectivo::where('activo', true)->first();

        // Inscripción activa — carga contexto completo para la ficha
        $inscripcion = $anioActivo
            ? Inscripcion::with([
                'grupo.anioLectivo',
                'grupo.grado.sede',
                'inscripcionMaterias.asignacion.materia',
                'inscripcionMaterias.asignacion.docente',
                'inscripcionMaterias.notas',
              ])
              ->where('estudiante_id', Auth::id())
              ->where('estado', 'activa')
              ->whereHas('grupo', fn ($q) =>
                  $q->where('anio_lectivo_id', $anioActivo->id)
              )
              ->first()
            : null;

        // Periodos del año activo para los tabs
        $periodos = $anioActivo
            ? Periodo::where('anio_lectivo_id', $anioActivo->id)
                ->orderBy('numero')
                ->get()
            : collect();

        return view('modulos.estudiante.notas.index', compact(
            'anioActivo',
            'inscripcion',
            'periodos'
        ));
    }

    /*
    |----------------------------------------------------------------------
    | porPeriodo
    |----------------------------------------------------------------------
    | Notas filtradas por un periodo específico.
    | Verifica que el periodo pertenezca al año en curso del estudiante.
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
                'grupo.anioLectivo',
                'grupo.grado.sede',
                'inscripcionMaterias.asignacion.materia',
                'inscripcionMaterias.asignacion.docente',
                'inscripcionMaterias.notas' => fn ($q) =>
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

        return view('modulos.estudiante.notas.periodo', compact(
            'periodo',
            'inscripcion',
            'periodos',
            'anioActivo'
        ));
    }
}