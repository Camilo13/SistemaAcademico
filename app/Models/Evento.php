<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo Evento
 * --------------------------------------------------
 * Representa los eventos institucionales.
 *
 * Reglas:
 * - Solo eventos activos se muestran públicamente.
 * - Solo eventos con fecha >= ahora se consideran próximos.
 */
class Evento extends Model
{
    use HasFactory;

    /**
     * Nombre explícito de la tabla (opcional pero profesional).
     */
    protected $table = 'eventos';

    /**
     * Campos asignables masivamente.
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'lugar',
        'fecha_evento',
        'activo',
    ];

    /**
     * Conversión automática de tipos.
     */
    protected $casts = [
        'fecha_evento' => 'datetime',
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeProximos(Builder $query): Builder
    {
        return $query->where('fecha_evento', '>=', now());
    }

    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderBy('fecha_evento', 'asc');
    }
}
