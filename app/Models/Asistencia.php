<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/*
|--------------------------------------------------------------------------
| Modelo Asistencia
|--------------------------------------------------------------------------
| Representa el conteo de faltas de un estudiante en una materia
| durante un periodo académico específico.
|
| Reglas académicas:
|   - Solo modificable si el periodo está abierto.
|   - Las faltas deben ser >= 0.
|   - El total = justificadas + injustificadas.
|--------------------------------------------------------------------------
*/

class Asistencia extends Model
{
    protected $table = 'asistencias';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'inscripcion_materia_id',
        'periodo_id',
        'faltas_justificadas',
        'faltas_injustificadas',
        'observacion',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'faltas_justificadas'   => 'integer',
        'faltas_injustificadas' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT — Integridad académica
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saving(function (Asistencia $asistencia) {

            // 1. Cargar relaciones necesarias
            $asistencia->loadMissing('periodo', 'inscripcionMateria.inscripcion');

            // 2. El periodo debe existir y estar abierto
            if (!$asistencia->periodo) {
                throw new RuntimeException('El periodo académico no existe.');
            }

            if ($asistencia->periodo->estaCerrado()) {
                throw new RuntimeException(
                    'No se puede registrar o modificar asistencia en un periodo cerrado.'
                );
            }

            // 3. La inscripción de materia debe estar activa
            if (
                $asistencia->inscripcionMateria &&
                !$asistencia->inscripcionMateria->estaActiva()
            ) {
                throw new RuntimeException(
                    'No se puede registrar asistencia en una materia retirada.'
                );
            }

            // 4. La inscripción del estudiante debe estar activa
            if (
                $asistencia->inscripcionMateria?->inscripcion &&
                !$asistencia->inscripcionMateria->inscripcion->estaActiva()
            ) {
                throw new RuntimeException(
                    'No se puede registrar asistencia en una inscripción inactiva.'
                );
            }

            // 5. Validar que los valores sean >= 0
            if ($asistencia->faltas_justificadas < 0 || $asistencia->faltas_injustificadas < 0) {
                throw new RuntimeException('El número de faltas no puede ser negativo.');
            }
        });

        static::deleting(function (Asistencia $asistencia) {

            $asistencia->loadMissing('periodo');

            if ($asistencia->periodo && $asistencia->periodo->estaCerrado()) {
                throw new RuntimeException(
                    'No se pueden eliminar registros de asistencia de un periodo cerrado.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function inscripcionMateria(): BelongsTo
    {
        return $this->belongsTo(InscripcionMateria::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePorPeriodo(Builder $query, int $periodoId): Builder
    {
        return $query->where('periodo_id', $periodoId);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS ACADÉMICOS
    |--------------------------------------------------------------------------
    */

    /**
     * Total de faltas (justificadas + injustificadas).
     */
    public function totalFaltas(): int
    {
        return $this->faltas_justificadas + $this->faltas_injustificadas;
    }

    /**
     * Determina si el estudiante supera el umbral de inasistencia.
     * Regla típica: más del 20% de las clases del periodo.
     * El parámetro $totalClases viene del docente al registrar.
     */
    public function superaUmbral(int $totalClases, float $porcentaje = 20.0): bool
    {
        if ($totalClases <= 0) {
            return false;
        }

        return ($this->totalFaltas() / $totalClases * 100) >= $porcentaje;
    }
}
