<?php

namespace App\Services;

use App\Models\Inscripcion;
use App\Models\InscripcionMateria;
use App\Models\Nota;
use Illuminate\Support\Collection;

class CalculoAcademicoService
{
    /*
    |--------------------------------------------------------------------------
    | PROMEDIO POR MATERIA
    |--------------------------------------------------------------------------
    */

    /**
     * Calcula el promedio final de una materia.
     *
     * - Ignora materias retiradas.
     * - Retorna null si no hay notas.
     * - Aplica regla académica de aproximación.
     */
    public function calcularPromedioMateria(
        InscripcionMateria $inscripcionMateria
    ): ?float {

        if ($inscripcionMateria->estado === 'retirada') {
            return null;
        }

        $notas = $inscripcionMateria->relationLoaded('notas')
            ? $inscripcionMateria->notas
            : $inscripcionMateria->notas()->get();

        if ($notas->isEmpty()) {
            return null;
        }

        $promedio = $notas->avg('nota');

        return Nota::aproximarADecima((float) $promedio);
    }

    /*
    |--------------------------------------------------------------------------
    | APROBACIÓN DE MATERIA
    |--------------------------------------------------------------------------
    */

    public function estaAprobadaMateria(
        InscripcionMateria $inscripcionMateria
    ): bool {

        $promedio = $this->calcularPromedioMateria($inscripcionMateria);

        return !is_null($promedio) && $promedio >= 3.0;
    }

    /*
    |--------------------------------------------------------------------------
    | PROMEDIO GENERAL ANUAL
    |--------------------------------------------------------------------------
    */

    /**
     * Calcula el promedio general del año lectivo.
     *
     * - Solo materias activas.
     * - Solo materias con promedio válido.
     */
    public function calcularPromedioAnual(
        Inscripcion $inscripcion
    ): ?float {

        $inscripcion->loadMissing('inscripcionMaterias.notas');

        $materiasActivas = $inscripcion->inscripcionMaterias
            ->where('estado', 'activa');

        if ($materiasActivas->isEmpty()) {
            return null;
        }

        $promedios = [];

        foreach ($materiasActivas as $materia) {

            $promedio = $this->calcularPromedioMateria($materia);

            if (!is_null($promedio)) {
                $promedios[] = $promedio;
            }
        }

        if (empty($promedios)) {
            return null;
        }

        $promedioGeneral = array_sum($promedios) / count($promedios);

        return Nota::aproximarADecima($promedioGeneral);
    }

    /*
    |--------------------------------------------------------------------------
    | APROBACIÓN DEL AÑO
    |--------------------------------------------------------------------------
    */

    /**
     * Regla actual:
     * - Todas las materias activas deben estar aprobadas.
     */
    public function estaAprobadoAnio(
        Inscripcion $inscripcion
    ): bool {

        $inscripcion->loadMissing('inscripcionMaterias.notas');

        $materiasActivas = $inscripcion->inscripcionMaterias
            ->where('estado', 'activa');

        foreach ($materiasActivas as $materia) {

            if (!$this->estaAprobadaMateria($materia)) {
                return false;
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | RESUMEN COMPLETO DEL AÑO
    |--------------------------------------------------------------------------
    */

    public function resumenAnual(
        Inscripcion $inscripcion
    ): array {

        $inscripcion->loadMissing('inscripcionMaterias.notas');

        $materiasActivas = $inscripcion->inscripcionMaterias
            ->where('estado', 'activa');

        $detalleMaterias = [];

        foreach ($materiasActivas as $materia) {

            $promedio = $this->calcularPromedioMateria($materia);

            $detalleMaterias[] = [
                'inscripcion_materia_id' => $materia->id,
                'promedio'               => $promedio,
                'aprobada'               => !is_null($promedio)
                    ? $promedio >= 3.0
                    : false,
            ];
        }

        return [
            'promedio_general' => $this->calcularPromedioAnual($inscripcion),
            'aprobado_anio'    => $this->estaAprobadoAnio($inscripcion),
            'materias'         => $detalleMaterias,
        ];
    }
}