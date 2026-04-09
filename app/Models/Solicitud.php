<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    /* ==================================================
     | CONFIGURACIÓN BÁSICA
     ================================================== */

    protected $table = 'solicitudes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'apellidos',
        'identificacion',
        'correo',
        'ubicacion',
        'contacto',
        'rol',
        'estado',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    /* ==================================================
     | CONSTANTES DE NEGOCIO
     ================================================== */

    // Roles permitidos
    public const ROL_ESTUDIANTE = 'estudiante';
    public const ROL_DOCENTE    = 'docente';

    // Estados del flujo
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_APROBADA  = 'aprobada';
    public const ESTADO_RECHAZADA = 'rechazada';

    /* ==================================================
     | SCOPES (Consultas reutilizables)
     ================================================== */

    /**
     * Solicitudes pendientes de revisión
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    /**
     * Solicitudes aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', self::ESTADO_APROBADA);
    }

    /**
     * Solicitudes rechazadas
     */
    public function scopeRechazadas($query)
    {
        return $query->where('estado', self::ESTADO_RECHAZADA);
    }
}
