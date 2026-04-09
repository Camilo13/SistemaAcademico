<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class Nota extends Model
{
    protected $table = 'notas';

    protected $fillable = [
        'inscripcion_materia_id',
        'periodo_id',
        'nota',
        'observacion',
    ];

    protected $casts = [
        'nota' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | EVENTOS - Integridad Académica Total
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saving(function (Nota $nota) {

            /*
            |--------------------------------------------------------------------------
            | 1️ Validar rango
            |--------------------------------------------------------------------------
            */
            if ($nota->nota < 0 || $nota->nota > 5) {
                throw new RuntimeException(
                    'La nota debe estar entre 0.00 y 5.00.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 2️ Cargar relaciones necesarias
            |--------------------------------------------------------------------------
            */
            $nota->loadMissing(
                'periodo',
                'inscripcionMateria.inscripcion',
                'inscripcionMateria.asignacion',
                'inscripcionMateria.inscripcion.grupo'
            );

            $inscripcionMateria = $nota->inscripcionMateria;

            if (!$inscripcionMateria) {
                throw new RuntimeException(
                    'La inscripción de materia no existe.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 3️ Validar estados activos
            |--------------------------------------------------------------------------
            */

            if (!$inscripcionMateria->estaActiva()) {
                throw new RuntimeException(
                    'No se pueden registrar notas en materias retiradas.'
                );
            }

            if (!$inscripcionMateria->inscripcion->estaActiva()) {
                throw new RuntimeException(
                    'No se pueden registrar notas en inscripciones inactivas.'
                );
            }

            if (!$inscripcionMateria->asignacion->activa) {
                throw new RuntimeException(
                    'La asignación docente está inactiva.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 4️ Validar periodo
            |--------------------------------------------------------------------------
            */

            if (!$nota->periodo) {
                throw new RuntimeException(
                    'El periodo académico no existe.'
                );
            }

            if ($nota->periodo->estaCerrado()) {
                throw new RuntimeException(
                    'No se pueden registrar o modificar notas en un periodo cerrado.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 5️ Validar coherencia Año Lectivo
            |--------------------------------------------------------------------------
            */

            $grupo = $inscripcionMateria->inscripcion->grupo;

            if (
                $grupo->anio_lectivo_id !== $nota->periodo->anio_lectivo_id
            ) {
                throw new RuntimeException(
                    'El periodo no pertenece al mismo año lectivo del grupo.'
                );
            }
        });

        static::deleting(function (Nota $nota) {

            $nota->loadMissing('periodo');

            if ($nota->periodo && $nota->periodo->estaCerrado()) {
                throw new RuntimeException(
                    'No se pueden eliminar notas de un periodo cerrado.'
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

    public function estaEnPeriodoAbierto(): bool
    {
        return $this->periodo && $this->periodo->estaAbierto();
    }

    public function notaFormateada(): string
    {
        return number_format((float) $this->nota, 1, ',', '.');
    }

    /**
     * Regla oficial institucional de aproximación.
     */
    public static function aproximarADecima(float $valor): float
    {
        return round($valor, 1, PHP_ROUND_HALF_UP);
    }
}