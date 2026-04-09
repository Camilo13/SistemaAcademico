<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grado extends Model
{
    use HasFactory;

    protected $table = 'grados';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'sede_id',
        'nombre',
        'nivel',
        'tipo',
        'activo',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts automáticos
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'activo' => 'boolean',
        'nivel'  => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT - Reglas de integridad
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::deleting(function (Grado $grado) {

            if (
                $grado->grupos()->exists() ||
                $grado->materias()->exists()
            ) {
                throw new \RuntimeException(
                    'No se puede eliminar un grado con grupos o materias asociadas.'
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
     * Un grado pertenece a una sede.
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * Un grado tiene muchos grupos.
     */
    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class);
    }

    /**
     * Un grado tiene muchas materias.
     */
    public function materias(): HasMany
    {
        return $this->hasMany(Materia::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderBy('nivel');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORES
    |--------------------------------------------------------------------------
    */

    /**
     * Devuelve formato institucional del grado.
     * Ejemplo: 6° - Secundaria
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nivel}° - {$this->tipo}";
    }
}