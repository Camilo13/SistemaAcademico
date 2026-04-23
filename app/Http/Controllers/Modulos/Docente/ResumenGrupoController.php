<?php

namespace App\Http\Controllers\Modulos\Docente;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Services\CalculoAcademicoService;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| ResumenGrupoController
|--------------------------------------------------------------------------
| Muestra al docente el resumen académico completo de todos los
| estudiantes activos de uno de sus grupos asignados.
|
| Útil para:
|   - Identificar estudiantes en riesgo antes del cierre del año
|   - Preparar reuniones de padres con datos académicos
|   - Tener una vista de conjunto del rendimiento del grupo
|
| Ruta: GET /docente/grupos/{grupo}/resumen → docente.grupos.resumen
|--------------------------------------------------------------------------
*/

class ResumenGrupoController extends Controller
{
    public function __construct(
        protected CalculoAcademicoService $calculo
    ) {}

    /*
    |----------------------------------------------------------------------
    | show
    |----------------------------------------------------------------------
    | Verifica que el docente tenga asignación activa en el grupo,
    | luego carga todas las inscripciones activas y calcula el
    | resumen por estudiante.
    |----------------------------------------------------------------------
    */
    public function show(Grupo $grupo)
    {
        // Seguridad: el docente debe tener al menos una asignación
        // activa en este grupo
        $tieneAcceso = $grupo->asignaciones()
            ->where('docente_id', Auth::id())
            ->where('activa', true)
            ->exists();

        abort_unless($tieneAcceso, 403, 'No tienes asignaciones activas en este grupo.');

        // Cargar grupo con sus relaciones de contexto
        $grupo->load(['grado', 'anioLectivo']);

        // Todas las inscripciones activas del grupo con sus materias y notas
        $inscripciones = $grupo->inscripciones()
            ->with([
                'estudiante',
                'inscripcionMaterias.notas',
            ])
            ->where('estado', 'activa')
            ->get()
            ->sortBy(fn($i) => $i->estudiante->apellidos ?? '');

        // Construir resumen por estudiante
        $estudiantes = $inscripciones->map(function ($inscripcion) {

            $promedio  = $this->calculo->calcularPromedioAnual($inscripcion);
            $aprobado  = $this->calculo->estaAprobadoAnio($inscripcion);

            $materias  = $inscripcion->inscripcionMaterias
                ->where('estado', 'activa');

            $aprobadas  = $materias->filter(fn($m) =>
                $this->calculo->estaAprobadaMateria($m)
            )->count();

            $reprobadas = $materias->filter(fn($m) =>
                !$this->calculo->estaAprobadaMateria($m) &&
                !is_null($this->calculo->calcularPromedioMateria($m))
            )->count();

            $sinCalificar = $materias->filter(fn($m) =>
                is_null($this->calculo->calcularPromedioMateria($m))
            )->count();

            return [
                'inscripcion_id'  => $inscripcion->id,
                'nombre'          => $inscripcion->estudiante->nombre_completo ?? '—',
                'identificacion'  => $inscripcion->estudiante->identificacion ?? '—',
                'promedio'        => $promedio,
                'aprobado'        => $aprobado,
                'total_materias'  => $materias->count(),
                'aprobadas'       => $aprobadas,
                'reprobadas'      => $reprobadas,
                'sin_calificar'   => $sinCalificar,
            ];
        })->values();

        // Estadísticas del grupo
        $stats = [
            'total'         => $estudiantes->count(),
            'aprobados'     => $estudiantes->where('aprobado', true)->count(),
            'reprobados'    => $estudiantes->where('aprobado', false)
                                ->where('promedio', '!=', null)->count(),
            'sin_calificar' => $estudiantes->whereNull('promedio')->count(),
        ];

        return view('modulos.docente.grupos.resumen', compact(
            'grupo',
            'estudiantes',
            'stats'
        ));
    }
}