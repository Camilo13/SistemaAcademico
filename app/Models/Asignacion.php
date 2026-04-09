<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use RuntimeException;

class Asignacion extends Model
{
    protected $table = 'asignaciones';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'docente_id',
        'materia_id',
        'grupo_id',
        'activa',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'activa' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT - Reglas de integridad
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saving(function (Asignacion $asignacion) {

            // La materia debe pertenecer al mismo grado del grupo
            if (
                $asignacion->materia &&
                $asignacion->grupo &&
                $asignacion->materia->grado_id !== $asignacion->grupo->grado_id
            ) {
                throw new RuntimeException(
                    'La materia no pertenece al mismo grado del grupo.'
                );
            }
        });

        static::deleting(function (Asignacion $asignacion) {

            if ($asignacion->notas()->exists()) {
                throw new RuntimeException(
                    'No se puede eliminar una asignación que tiene notas registradas.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function inscripcionMaterias(): HasMany
    {
        return $this->hasMany(InscripcionMateria::class);
    }

    /**
     * Notas de esta asignación a través de InscripcionMateria.
     *
     * La tabla notas NO tiene asignacion_id directamente —
     * tiene inscripcion_materia_id, que sí apunta a asignacion_id.
     *
     * Ruta: asignaciones → inscripcion_materias → notas
     */
    public function notas(): HasManyThrough
    {
        return $this->hasManyThrough(
            Nota::class,
            InscripcionMateria::class,
            'asignacion_id',          // FK en inscripcion_materias → asignaciones
            'inscripcion_materia_id', // FK en notas → inscripcion_materias
            'id',                     // PK en asignaciones
            'id'                      // PK en inscripcion_materias
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORES
    |--------------------------------------------------------------------------
    */

    /**
     * Año lectivo del grupo asignado.
     * Se accede como propiedad: $asignacion->anioLectivo
     */
    public function getAnioLectivoAttribute(): ?AnioLectivo
    {
        return $this->grupo?->anioLectivo;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActiva(Builder $query): Builder
    {
        return $query->where('activa', true);
    }

    public function scopePorAnio(Builder $query, int $anioId): Builder
    {
        return $query->whereHas('grupo', function ($q) use ($anioId) {
            $q->where('anio_lectivo_id', $anioId);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function activar(): void
    {
        $this->update(['activa' => true]);
    }

    public function desactivar(): void
    {
        $this->update(['activa' => false]);
    }
}
