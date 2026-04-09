<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periodo extends Model
{
    protected $table = 'periodos';

    protected $fillable = [
        'anio_lectivo_id',
        'numero',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'abierto',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'abierto'      => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT - Reglas de dominio académico
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saving(function (Periodo $periodo) {

            // 1️ Validación básica de fechas
            if ($periodo->fecha_inicio > $periodo->fecha_fin) {
                throw new \RuntimeException(
                    'La fecha de inicio no puede ser mayor que la fecha de fin.'
                );
            }

            // 2️ Validar existencia del año lectivo
            $anio = $periodo->anioLectivo;

            if (!$anio) {
                throw new \RuntimeException(
                    'El periodo debe pertenecer a un año lectivo válido.'
                );
            }

            // 3️ Validar que esté dentro del rango del año
            if (
                $periodo->fecha_inicio < $anio->fecha_inicio ||
                $periodo->fecha_fin > $anio->fecha_fin
            ) {
                throw new \RuntimeException(
                    'Las fechas del periodo deben estar dentro del rango del año lectivo.'
                );
            }

            // 4️ Máximo 3 periodos por año (regla institucional)
            if (!$periodo->exists) {

                $count = self::where('anio_lectivo_id', $periodo->anio_lectivo_id)->count();

                if ($count >= 3) {
                    throw new \RuntimeException(
                        'Un año lectivo solo puede tener máximo 3 periodos.'
                    );
                }
            }

            // 5️ Nombre automático si no se envía
            if (empty($periodo->nombre)) {
                $periodo->nombre = 'Periodo ' . $periodo->numero;
            }
        });

        static::deleting(function (Periodo $periodo) {

            if ($periodo->notas()->exists()) {
                throw new \RuntimeException(
                    'No se puede eliminar un periodo que tenga notas registradas.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function anioLectivo(): BelongsTo
    {
        return $this->belongsTo(AnioLectivo::class);
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeAbierto(Builder $query): Builder
    {
        return $query->where('abierto', true);
    }

    public function scopeCerrado(Builder $query): Builder
    {
        return $query->where('abierto', false);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function estaAbierto(): bool
    {
        return $this->abierto === true;
    }

    public function estaCerrado(): bool
    {
        return $this->abierto === false;
    }

    public function estaEnCurso(): bool
    {
        return now()->between(
            $this->fecha_inicio,
            $this->fecha_fin
        );
    }

    public function cerrar(): void
    {
        $this->update(['abierto' => false]);
    }

    public function abrir(): void
    {
        $this->update(['abierto' => true]);
    }
}