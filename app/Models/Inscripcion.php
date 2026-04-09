<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use RuntimeException;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'estudiante_id',
        'grupo_id',
        'estado',
        'fecha_inscripcion',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'fecha_inscripcion' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT - Reglas de integridad
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saving(function (Inscripcion $inscripcion) {

            // Validar que el grupo esté activo
            if ($inscripcion->grupo && !$inscripcion->grupo->activo) {
                throw new RuntimeException(
                    'No se puede inscribir en un grupo inactivo.'
                );
            }
        });

        static::deleting(function (Inscripcion $inscripcion) {

            if (
                $inscripcion->inscripcionMaterias()->exists() ||
                $inscripcion->notas()->exists()
            ) {
                throw new RuntimeException(
                    'No se puede eliminar una inscripción con información académica asociada.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'estudiante_id');
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
     * Notas del estudiante a través de sus materias inscritas.
     *
     * Ruta: inscripciones → inscripcion_materias → notas
     */
    public function notas(): HasManyThrough
    {
        return $this->hasManyThrough(
            Nota::class,
            InscripcionMateria::class,
            'inscripcion_id',         // FK en inscripcion_materias
            'inscripcion_materia_id', // FK en notas
            'id',                     // PK en inscripciones
            'id'                      // PK en inscripcion_materias
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORES
    |--------------------------------------------------------------------------
    */

    /**
     * Acceso indirecto al año lectivo a través del grupo.
     * Se accede como propiedad: $inscripcion->anioLectivo
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
        return $query->where('estado', 'activa');
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

    public function estaActiva(): bool
    {
        return $this->estado === 'activa';
    }

    public function retirar(): void
    {
        $this->update(['estado' => 'retirada']);
    }

    public function finalizar(): void
    {
        $this->update(['estado' => 'finalizada']);
    }
}
