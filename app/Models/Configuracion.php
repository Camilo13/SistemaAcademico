<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
|--------------------------------------------------------------------------
| Modelo Configuracion
|--------------------------------------------------------------------------
| Almacena parámetros globales del sistema como clave → valor.
|
| Claves conocidas definidas como constantes de clase.
| Uso recomendado:
|   Configuracion::obtener(Configuracion::NOMBRE_INSTITUCION)
|--------------------------------------------------------------------------
*/

class Configuracion extends Model
{
    protected $table = 'configuracion';

    const FIRMA_RECTOR       = 'firma_rector';
    const NOMBRE_INSTITUCION = 'nombre_institucion';
    const NIT_INSTITUCION    = 'nit_institucion';
    const MUNICIPIO          = 'municipio';
    const DEPARTAMENTO       = 'departamento';
    const RESOLUCION         = 'resolucion';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Devuelve el valor de una clave de configuración o $default si no existe.
     */
    public static function obtener(string $clave, string $default = ''): string
    {
        return static::where('clave', $clave)->value('valor') ?? $default;
    }
}
