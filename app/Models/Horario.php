<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| Modelo Horario
|--------------------------------------------------------------------------
| Representa una franja horaria semanal de una asignación académica.
|
| Campos:
|   asignacion_id · dia_semana · bloque · timestamps
|
| Bloques fijos institucionales:
|   1 → 7:00 – 8:00     (Mañana)
|   2 → 8:00 – 9:00     (Mañana)
|   3 → 9:30 – 10:30    (Media mañana, tras refrigerio)
|   4 → 10:30 – 11:30   (Media mañana)
|   5 → 13:00 – 14:00   (Tarde, tras almuerzo)
|   6 → 14:00 – 15:00   (Tarde)
|--------------------------------------------------------------------------
*/

class Horario extends Model
{
    /*
    |----------------------------------------------------------------------
    | Bloques horarios fijos de la institución
    |----------------------------------------------------------------------
    | Constante de consulta: Horario::BLOQUES[3] → ['inicio'=>'9:30', ...]
    */
    const BLOQUES = [
        1 => ['inicio' => '7:00',  'fin' => '8:00',  'sesion' => 'Mañana'],
        2 => ['inicio' => '8:00',  'fin' => '9:00',  'sesion' => 'Mañana'],
        3 => ['inicio' => '9:30',  'fin' => '10:30', 'sesion' => 'Media mañana'],
        4 => ['inicio' => '10:30', 'fin' => '11:30', 'sesion' => 'Media mañana'],
        5 => ['inicio' => '13:00', 'fin' => '14:00', 'sesion' => 'Tarde'],
        6 => ['inicio' => '14:00', 'fin' => '15:00', 'sesion' => 'Tarde'],
    ];

    const DIAS = [
        'lunes', 'martes', 'miercoles', 'jueves', 'viernes',
    ];

    const DIAS_LABEL = [
        'lunes'     => 'Lunes',
        'martes'    => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves'    => 'Jueves',
        'viernes'   => 'Viernes',
    ];

    protected $fillable = [
        'asignacion_id',
        'dia_semana',
        'bloque',
    ];

    protected $casts = [
        'bloque' => 'integer',
    ];

    /*
    |----------------------------------------------------------------------
    | Relaciones
    |----------------------------------------------------------------------
    */

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class);
    }

    /*
    |----------------------------------------------------------------------
    | Accessors helpers
    |----------------------------------------------------------------------
    */

    /** Retorna la hora de inicio del bloque, ej: '7:00' */
    public function getHoraInicioAttribute(): string
    {
        return self::BLOQUES[$this->bloque]['inicio'] ?? '—';
    }

    /** Retorna la hora de fin del bloque, ej: '8:00' */
    public function getHoraFinAttribute(): string
    {
        return self::BLOQUES[$this->bloque]['fin'] ?? '—';
    }

    /** Retorna la sesión del bloque, ej: 'Mañana' */
    public function getSesionAttribute(): string
    {
        return self::BLOQUES[$this->bloque]['sesion'] ?? '—';
    }

    /** Retorna el rango formateado, ej: '7:00 – 8:00' */
    public function getRangoAttribute(): string
    {
        return "{$this->hora_inicio} – {$this->hora_fin}";
    }
}
