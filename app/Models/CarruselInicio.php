<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
|--------------------------------------------------------------------------
| Modelo CarruselInicio
|--------------------------------------------------------------------------
| Representa una imagen del carrusel del hero en la página de inicio.
| No contiene texto ni lógica de presentación.
*/

class CarruselInicio extends Model
{
    use HasFactory;

    /**
     * Asignación masiva permitida
     */
    protected $fillable = [
        'imagen',
        'orden',
        'activo',
    ];

    /**
     * Scope: solo imágenes activas y ordenadas
     */
    public function scopeActivosOrdenados($query)
    {
        return $query
            ->where('activo', true)
            ->orderBy('orden');
    }
}
