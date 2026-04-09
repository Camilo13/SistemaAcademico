<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

class InscripcionMateria extends Model
{
    protected $table = 'inscripcion_materias';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'inscripcion_id',
        'asignacion_id',
        'grupo_id',
        'estado',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT - Reglas de integridad estructural
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saving(function (InscripcionMateria $registro) {

            $inscripcion = $registro->inscripcion;
            $asignacion  = $registro->asignacion;

            if (!$inscripcion || !$asignacion) {
                throw new RuntimeException(
                    'Inscripción o asignación inválida.'
                );
            }

            // 1️ Validar inscripción activa
            if (!$inscripcion->estaActiva()) {
                throw new RuntimeException(
                    'No se pueden agregar materias a una inscripción inactiva.'
                );
            }

            // 2️ Validar asignación activa
            if (!$asignacion->activa) {
                throw new RuntimeException(
                    'No se puede usar una asignación inactiva.'
                );
            }

            // 3️ Validar coherencia de grupo
            if (
                $inscripcion->grupo_id !== $asignacion->grupo_id ||
                $registro->grupo_id !== $inscripcion->grupo_id
            ) {
                throw new RuntimeException(
                    'La asignación no pertenece al mismo grupo que la inscripción.'
                );
            }
        });

        static::deleting(function (InscripcionMateria $registro) {

            if ($registro->notas()->exists()) {
                throw new RuntimeException(
                    'No se puede eliminar una materia inscrita con notas registradas.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActiva(Builder $query): Builder
    {
        return $query->where('estado', 'activa');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function retirar(): void
    {
        $this->update(['estado' => 'retirada']);
    }

    public function estaActiva(): bool
    {
        return $this->estado === 'activa';
    }
}