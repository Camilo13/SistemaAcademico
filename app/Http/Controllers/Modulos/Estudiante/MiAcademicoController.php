<?php

namespace App\Http\Controllers\Modulos\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\AnioLectivo;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| MiAcademicoController
|--------------------------------------------------------------------------
| Muestra al estudiante autenticado su historial de inscripciones y
| las materias del año lectivo activo.
|
| Rutas:
|   GET /estudiante/academico/inscripciones  →  estudiante.academico.inscripciones
|   GET /estudiante/academico/materias       →  estudiante.academico.materias
|--------------------------------------------------------------------------
*/

class MiAcademicoController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | inscripciones
    |----------------------------------------------------------------------
    | Muestra todas las inscripciones del estudiante (todos los años),
    | ordenadas de más reciente a más antigua.
    |----------------------------------------------------------------------
    */
    public function inscripciones()
    {
        $inscripciones = Inscripcion::with(['grupo.grado', 'grupo.anioLectivo'])
            ->where('estudiante_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('modulos.estudiante.academico.inscripcion', compact('inscripciones'));
    }

    /*
    |----------------------------------------------------------------------
    | materias
    |----------------------------------------------------------------------
    | Muestra las materias inscritas del estudiante en el año activo.
    | Si no hay año activo o el estudiante no está inscrito, vista vacía.
    |----------------------------------------------------------------------
    */
    public function materias()
    {
        // Año lectivo activo
        $anioActivo = AnioLectivo::where('activo', true)->first();

        // Inscripción activa del estudiante en el año activo
        $inscripcion = null;
        $materiasInscritas = collect();

        if ($anioActivo) {
            $inscripcion = Inscripcion::with([
                    'grupo.grado',
                    'inscripcionMaterias.asignacion.materia',
                    'inscripcionMaterias.asignacion.docente',
                ])
                ->where('estudiante_id', Auth::id())
                ->where('estado', 'activa')
                ->whereHas('grupo', fn ($q) =>
                    $q->where('anio_lectivo_id', $anioActivo->id)
                )
                ->first();

            $materiasInscritas = $inscripcion
                ? $inscripcion->inscripcionMaterias->where('estado', 'activa')
                : collect();
        }

        return view('modulos.estudiante.academico.materias', compact(
            'anioActivo',
            'inscripcion',
            'materiasInscritas'
        ));
    }
}
