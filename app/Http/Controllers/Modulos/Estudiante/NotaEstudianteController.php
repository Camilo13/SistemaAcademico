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
    | Muestra todas las notas del estudiante en el año activo,
    | agrupadas por materia. Incluye el promedio por materia.
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $anioActivo = AnioLectivo::where('activo', true)->first();

        // Inscripción activa del año en curso
        $inscripcion = $anioActivo
            ? Inscripcion::with([
                'grupo.anioLectivo',
                'inscripcionMaterias.asignacion.materia',
                'inscripcionMaterias.notas.periodo',
              ])
              ->where('estudiante_id', Auth::id())
              ->where('estado', 'activa')
              ->whereHas('grupo', fn ($q) =>
                  $q->where('anio_lectivo_id', $anioActivo->id)
              )
              ->first()
            : null;

        // Periodos del año activo para los tabs/filtros
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
    | Muestra las notas filtradas por un periodo específico.
    | Verifica que el periodo pertenezca al año en curso del estudiante.
    |----------------------------------------------------------------------
    */
    public function porPeriodo(Periodo $periodo)
    {
        $anioActivo = AnioLectivo::where('activo', true)->first();

        // El periodo debe pertenecer al año activo
        abort_unless(
            $anioActivo && $periodo->anio_lectivo_id === $anioActivo->id,
            404
        );

        // Inscripción activa del estudiante en ese año
        $inscripcion = Inscripcion::with([
                'grupo',
                'inscripcionMaterias.asignacion.materia',
                'inscripcionMaterias.notas' => fn ($q) =>
                    $q->where('periodo_id', $periodo->id),
            ])
            ->where('estudiante_id', Auth::id())
            ->where('estado', 'activa')
            ->whereHas('grupo', fn ($q) =>
                $q->where('anio_lectivo_id', $anioActivo->id)
            )
            ->first();

        // Todos los periodos del año (para la navegación entre periodos)
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
