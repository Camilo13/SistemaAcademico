<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materias';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'grado_id',
        'codigo',
        'nombre',
        'intensidad_horaria',
        'activa',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts automáticos
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'activa'             => 'boolean',
        'intensidad_horaria' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT - Reglas de integridad
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::deleting(function (Materia $materia) {

            if ($materia->asignaciones()->exists()) {
                throw new RuntimeException(
                    'No se puede eliminar la materia porque tiene asignaciones registradas.'
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
     * Una materia pertenece a un grado.
     */
    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class);
    }

    /**
     * Una materia puede estar en múltiples asignaciones (docente + grupo).
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activa', true);
    }

    public function scopeOrdenadas(Builder $query): Builder
    {
        return $query->orderBy('nombre');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE NEGOCIO
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