<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/*
|--------------------------------------------------------------------------
| Modelo User
|--------------------------------------------------------------------------
| Representa a los usuarios autenticados del sistema académico.
| Es la ÚNICA entidad que gestiona:
|   - Autenticación y sesiones
|   - Contraseñas hasheadas
|   - Roles del sistema (administrador · docente · estudiante)
|
| Los usuarios se crean de dos formas:
|   1. Aprobando una Solicitud (SolicitudAdminController / GestionController)
|   2. Directamente por el administrador
|
| Campos de la tabla `users`:
|   id · nombre · apellidos · identificacion(unique) · correo(index)
|   ubicacion(nullable) · contacto(nullable) · password · rol(enum,index)
|   activo(bool,default:true,index) · remember_token · timestamps
|--------------------------------------------------------------------------
*/

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Constantes de rol
    |--------------------------------------------------------------------------
    | Usar siempre estas constantes en lugar de strings literales para
    | evitar errores de tipeo y facilitar refactorizaciones.
    |
    | Uso:
    |   User::ROL_DOCENTE          → 'docente'
    |   $user->rol === User::ROL_ESTUDIANTE
    */

    const ROL_ADMINISTRADOR = 'administrador';
    const ROL_DOCENTE       = 'docente';
    const ROL_ESTUDIANTE    = 'estudiante';

    /*
    |--------------------------------------------------------------------------
    | Campos permitidos para asignación masiva
    |--------------------------------------------------------------------------
    | Todos los campos de la migración excepto id, remember_token y timestamps
    | (estos los gestiona Laravel automáticamente).
    */

    protected $fillable = [
        'nombre',
        'apellidos',
        'identificacion',
        'correo',
        'ubicacion',
        'contacto',
        'password',
        'rol',
        'activo',
    ];

    /*
    |--------------------------------------------------------------------------
    | Atributos ocultos al serializar
    |--------------------------------------------------------------------------
    | Protege credenciales sensibles en respuestas JSON o toArray().
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casting de atributos
    |--------------------------------------------------------------------------
    | Convierte tipos automáticamente al leer / escribir el modelo.
    | 'activo' → siempre boolean (true/false), nunca 1/0.
    | 'password' → se hashea automáticamente al asignarse.
    */

    protected $casts = [
        'activo'   => 'boolean',
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Columna de email para autenticación
    |--------------------------------------------------------------------------
    | Laravel usa 'email' por defecto; nuestro campo se llama 'correo'.
    | Esto afecta a Auth::attempt(), password resets, etc.
    */

    public function getAuthIdentifierName(): string
    {
        return 'id'; // Identificador de sesión = clave primaria
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Asignaciones docentes.
     * Un docente puede estar asignado a varias materias/grupos.
     * FK: asignaciones.docente_id → users.id
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'docente_id');
    }

    /**
     * Inscripciones de un estudiante.
     * Un estudiante puede estar inscrito en varios grupos a lo largo del tiempo.
     * FK: inscripciones.estudiante_id → users.id
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'estudiante_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes de consulta
    |--------------------------------------------------------------------------
    | Permiten escribir consultas expresivas y encadenables.
    |
    | Uso genérico:
    |   User::rol('docente')->get()
    |   User::rol(User::ROL_ESTUDIANTE)->orderBy('nombre')->get()
    |
    | Uso específico (más legible en los controladores):
    |   User::docentes()->get()
    |   User::estudiantes()->orderBy('nombre')->get()
    |   User::administradores()->first()
    |
    | Combinable con otros scopes:
    |   User::docentes()->activos()->get()
    |   User::estudiantes()->activos()->orderBy('apellidos')->paginate(20)
    */

    /**
     * Scope genérico — filtra por cualquier valor del campo rol.
     */
    public function scopeRol($query, string $rol)
    {
        return $query->where('rol', $rol);
    }

    /**
     * Scope específico para docentes.
     */
    public function scopeDocentes($query)
    {
        return $query->where('rol', self::ROL_DOCENTE);
    }

    /**
     * Scope específico para estudiantes.
     */
    public function scopeEstudiantes($query)
    {
        return $query->where('rol', self::ROL_ESTUDIANTE);
    }

    /**
     * Scope específico para administradores.
     */
    public function scopeAdministradores($query)
    {
        return $query->where('rol', self::ROL_ADMINISTRADOR);
    }

    /**
     * Scope para usuarios activos — combinable con cualquier scope de rol.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para usuarios inactivos.
     */
    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de instancia
    |--------------------------------------------------------------------------
    | Para verificar el rol de un usuario ya cargado:
    |
    |   if ($user->esDocente()) { ... }
    |   if (Auth::user()->esAdministrador()) { ... }
    */

    public function esAdministrador(): bool
    {
        return $this->rol === self::ROL_ADMINISTRADOR;
    }

    public function esDocente(): bool
    {
        return $this->rol === self::ROL_DOCENTE;
    }

    public function esEstudiante(): bool
    {
        return $this->rol === self::ROL_ESTUDIANTE;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor: nombre completo
    |--------------------------------------------------------------------------
    | Devuelve "nombre apellidos" como un solo string.
    |
    | Uso:
    |   $user->nombre_completo  → "María García"
    |
    | Nota: los scopes usan orderBy('nombre') y orderBy('apellidos')
    | por separado porque la base de datos no puede indexar un accessor.
    */

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellidos}");
    }
}
