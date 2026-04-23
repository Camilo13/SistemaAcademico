<?php

namespace App\Services;

use App\Models\Inscripcion;
use App\Models\InscripcionMateria;
use Illuminate\Support\Collection;

class BoletinService
{
    protected CalculoAcademicoService $calculo;

    public function __construct(CalculoAcademicoService $calculo)
    {
        $this->calculo = $calculo;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERAR BOLETÍN ANUAL COMPLETO
    |--------------------------------------------------------------------------
    */

    public function generarBoletinAnual(
        Inscripcion $inscripcion
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Carga estructural completa (optimizada)
        |--------------------------------------------------------------------------
        */
        $inscripcion->load([
            'estudiante',
            'grupo.anioLectivo',
            'inscripcionMaterias.asignacion.materia',
            'inscripcionMaterias.asignacion.docente',
            'inscripcionMaterias.notas',
        ]);

        $materiasActivas = $inscripcion->inscripcionMaterias
            ->where('estado', 'activa');

        $detalleMaterias = $this->construirDetalleMaterias($materiasActivas);

        return [

            /*
            |--------------------------------------------------------------------------
            | DATOS GENERALES
            |--------------------------------------------------------------------------
            */
            'estudiante' => [
                'id'     => $inscripcion->estudiante->id,
                'nombre' => $inscripcion->estudiante->nombre_completo ?? 'N/A',
            ],

            'grupo' => $inscripcion->grupo->nombre ?? null,

            'anio_lectivo' =>
                $inscripcion->grupo->anioLectivo->nombre ?? null,

            'fecha_generacion' => now()->format('Y-m-d H:i:s'),

            /*
            |--------------------------------------------------------------------------
            | DETALLE ACADÉMICO
            |--------------------------------------------------------------------------
            */
            'materias' => $detalleMaterias,

            /*
            |--------------------------------------------------------------------------
            | RESUMEN GENERAL
            |--------------------------------------------------------------------------
            */
            'promedio_general' =>
                $this->calculo->calcularPromedioAnual($inscripcion),

            'aprobado_anio' =>
                $this->calculo->estaAprobadoAnio($inscripcion),

            'total_materias' =>
                $materiasActivas->count(),

            'materias_aprobadas' =>
                $detalleMaterias->where('aprobada', true)->count(),

            'materias_reprobadas' =>
                $detalleMaterias->where('aprobada', false)->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CONSTRUIR DETALLE DE MATERIAS
    |--------------------------------------------------------------------------
    */

    protected function construirDetalleMaterias(
        Collection $materias
    ): Collection {

        return $materias->map(function (
            InscripcionMateria $materia
        ) {

            $promedio =
                $this->calculo->calcularPromedioMateria($materia);

            return [
                'inscripcion_materia_id' => $materia->id,

                'materia_id' =>
                    $materia->asignacion->materia->id ?? null,

                'materia_nombre' =>
                    $materia->asignacion->materia->nombre ?? 'N/A',

                'docente_nombre' =>
                    $materia->asignacion->docente->nombre_completo ?? 'N/A',

                'total_notas' =>
                    $materia->notas->count(),

                'promedio' => $promedio,

                'aprobada' =>
                    !is_null($promedio) && $promedio >= 3.0,

                'estado_academico' =>
                    $this->resolverEstadoAcademico($promedio),
            ];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ESTADO ACADÉMICO DESCRIPTIVO
    |--------------------------------------------------------------------------
    */

    protected function resolverEstadoAcademico(
        ?float $promedio
    ): string {

        if (is_null($promedio)) {
            return 'Sin calificar';
        }

        if ($promedio >= 4.5) {
            return 'Desempeño Superior';
        }

        if ($promedio >= 4.0) {
            return 'Desempeño Alto';
        }

        if ($promedio >= 3.0) {
            return 'Desempeño Básico';
        }

        return 'Desempeño Bajo';
    }
}