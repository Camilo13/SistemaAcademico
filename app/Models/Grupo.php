<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

class Grupo extends Model
{
    protected $table = 'grupos';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'grado_id',
        'anio_lectivo_id',
        'nombre',
        'cupo_maximo',
        'activo',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'activo'       => 'boolean',
        'cupo_maximo'  => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT - Reglas de integridad
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::deleting(function (Grupo $grupo) {

            if (
                $grupo->inscripciones()->exists() ||
                $grupo->asignaciones()->exists()
            ) {
                throw new RuntimeException(
                    'No se puede eliminar un grupo con inscripciones o asignaciones registradas.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Un grupo pertenece a un grado.
     */
    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class);
    }

    /**
     * Un grupo pertenece a un año lectivo.
     */
    public function anioLectivo(): BelongsTo
    {
        return $this->belongsTo(AnioLectivo::class);
    }

    /**
     * Un grupo tiene muchas inscripciones.
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    /**
     * Un grupo tiene muchas asignaciones.
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORES
    |--------------------------------------------------------------------------
    */

    /**
     * Acceso indirecto a la sede a través del grado.
     * Se accede como propiedad: $grupo->sede
     */
    public function getSedeAttribute(): ?Sede
    {
        return $this->grado?->sede;
    }

    /**
     * Nombre institucional completo.
     * Ej: Sexto - A (2025)
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->grado->nombre} - {$this->nombre} ({$this->anioLectivo->nombre})";
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

    /**
     * Ordenar grupos por grado y luego por nombre (A, B, C...).
     * Usado en AsignacionController e InscripcionController.
     */
    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderBy('grado_id')->orderBy('nombre');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function estaActivo(): bool
    {
        return $this->activo === true;
    }

    public function cupoDisponible(): ?int
    {
        if (!$this->cupo_maximo) {
            return null;
        }

        return $this->cupo_maximo - $this->inscripciones()->count();
    }

    public function tieneCupo(): bool
    {
        if (!$this->cupo_maximo) {
            return true;
        }

        return $this->inscripciones()->count() < $this->cupo_maximo;
    }
}
