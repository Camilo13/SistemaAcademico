<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Sede extends Model
{
    protected $table = 'sedes';

    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'activa',
    ];

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
        static::deleting(function (Sede $sede) {

            // Si tiene grados asociados no se puede eliminar
            if ($sede->grados()->exists()) {
                throw new \RuntimeException(
                    'No se puede eliminar una sede con grados asociados.'
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
     * Una sede tiene muchos grados.
     */
    public function grados(): HasMany
    {
        return $this->hasMany(Grado::class);
    }

    /**
     * Acceso indirecto a grupos a través de grados.
     */
    public function grupos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Grupo::class,
            Grado::class,
            'sede_id',   // FK en grados
            'grado_id',  // FK en grupos
            'id',        // PK en sedes
            'id'         // PK en grados
        );
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