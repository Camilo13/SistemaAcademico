<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

class AnioLectivo extends Model
{
    protected $table = 'anios_lectivos';

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activo'       => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT - Reglas de negocio críticas
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saving(function (AnioLectivo $anio) {

            // Validación de rango de fechas
            if ($anio->fecha_inicio > $anio->fecha_fin) {
                throw new \RuntimeException(
                    'La fecha de inicio no puede ser mayor que la fecha de fin.'
                );
            }

            // Garantizar único año activo (transaccional)
            if ($anio->activo) {
                DB::transaction(function () use ($anio) {
                    self::where('activo', true)
                        ->where('id', '!=', $anio->id)
                        ->update(['activo' => false]);
                });
            }
        });

        static::deleting(function (AnioLectivo $anio) {

            // No permitir eliminar si tiene periodos asociados
            if ($anio->periodos()->exists()) {
                throw new \RuntimeException(
                    'No se puede eliminar un año lectivo que tiene periodos registrados. ' .
                    'Elimina primero todos sus periodos.'
                );
            }

            // No permitir eliminar si tiene grupos asociados
            if ($anio->grupos()->exists()) {
                throw new \RuntimeException(
                    'No se puede eliminar un año lectivo con grupos asociados.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES DIRECTAS
    |--------------------------------------------------------------------------
    */

    /**
     * Un año lectivo tiene muchos periodos.
     */
    public function periodos(): HasMany
    {
        return $this->hasMany(Periodo::class);
    }

    /**
     * Un año lectivo tiene muchos grupos.
     */
    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES INDIRECTAS (a través de grupos)
    |--------------------------------------------------------------------------
    */

    /**
     * Inscripciones del año a través de grupos.
     */
    public function inscripciones(): HasManyThrough
    {
        return $this->hasManyThrough(
            Inscripcion::class,
            Grupo::class,
            'anio_lectivo_id', // FK en grupos
            'grupo_id',        // FK en inscripciones
            'id',              // PK en anios_lectivos
            'id'               // PK en grupos
        );
    }

    /**
     * Asignaciones del año a través de grupos.
     */
    public function asignaciones(): HasManyThrough
    {
        return $this->hasManyThrough(
            Asignacion::class,
            Grupo::class,
            'anio_lectivo_id',
            'grupo_id',
            'id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public static function vigente(): ?self
    {
        return self::activo()->first();
    }

    public static function vigenteOrFail(): self
    {
        return self::activo()->firstOrFail();
    }

    public function estaEnCurso(): bool
    {
        return now()->between(
            $this->fecha_inicio,
            $this->fecha_fin
        );
    }
}