<?php

namespace App\Http\Controllers\Modulos\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Throwable;

/*
|--------------------------------------------------------------------------
| UsuarioController
|--------------------------------------------------------------------------
| Gestión de usuarios del sistema por el administrador.
|
| Cambios respecto a la versión anterior:
|   - show() eliminado — la info de relaciones se muestra en edit()
|   - edit() carga inscripciones y asignaciones del usuario
|   - update() redirige a index (no a show)
|   - destroyBulk() nuevo — elimina múltiples usuarios en una operación
|   - try/catch en todas las escrituras
|   - trim + ?? '' en store() y update()
|
| Rutas:
|   GET    /admin/usuarios                    →  admin.usuarios.index
|   GET    /admin/usuarios/create             →  admin.usuarios.create
|   POST   /admin/usuarios                    →  admin.usuarios.store
|   GET    /admin/usuarios/{usuario}/edit     →  admin.usuarios.edit
|   PUT    /admin/usuarios/{usuario}          →  admin.usuarios.update
|   PATCH  /admin/usuarios/{usuario}/activar  →  admin.usuarios.activar
|   PATCH  /admin/usuarios/{usuario}/desactivar → admin.usuarios.desactivar
|   PATCH  /admin/usuarios/{usuario}/password →  admin.usuarios.password
|   DELETE /admin/usuarios/{usuario}          →  admin.usuarios.destroy
|   DELETE /admin/usuarios/bulk               →  admin.usuarios.destroyBulk
|--------------------------------------------------------------------------
*/

class UsuarioController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = User::query()->orderBy('apellidos')->orderBy('nombre');

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        if ($request->filled('buscar')) {
            $termino = $request->buscar;
            $query->where(function ($q) use ($termino) {
                $q->where('nombre',          'like', "%{$termino}%")
                  ->orWhere('apellidos',     'like', "%{$termino}%")
                  ->orWhere('identificacion','like', "%{$termino}%")
                  ->orWhere('correo',        'like', "%{$termino}%");
            });
        }

        $usuarios = $query->paginate(20)->withQueryString();

        $totales = [
            'total'           => User::count(),
            'administradores' => User::administradores()->count(),
            'docentes'        => User::docentes()->count(),
            'estudiantes'     => User::estudiantes()->count(),
            'inactivos'       => User::inactivos()->count(),
        ];

        return view('modulos.admin.usuarios.index', compact('usuarios', 'totales'));
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create()
    {
        return view('modulos.admin.usuarios.create');
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        // Limpiar espacios antes de validar
        $request->merge([
            'nombre'         => trim($request->nombre         ?? ''),
            'apellidos'      => trim($request->apellidos      ?? ''),
            'identificacion' => trim($request->identificacion ?? ''),
            'correo'         => trim($request->correo         ?? ''),
            'ubicacion'      => trim($request->ubicacion      ?? ''),
            'contacto'       => trim($request->contacto       ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglasStore(),
            $this->mensajes()
        );

        try {

            $validated['password'] = Hash::make($validated['password']);
            $validated['activo']   = $request->boolean('activo', true);

            User::create($validated);

            return redirect()
                ->route('admin.usuarios.index')
                ->with('exito', 'Usuario creado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_usuario' => 'Ocurrió un error al crear el usuario.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    | Carga las relaciones que antes mostraba show() para presentarlas
    | como sección informativa de solo lectura dentro del edit.
    */
    public function edit(User $usuario)
    {
        $usuario->load([
            'inscripciones.grupo.grado',
            'inscripciones.grupo.anioLectivo',
            'asignaciones.materia',
            'asignaciones.grupo.grado',
            'asignaciones.grupo.anioLectivo',
        ]);

        return view('modulos.admin.usuarios.edit', compact('usuario'));
    }

    /*
    |----------------------------------------------------------------------
    | update
    |----------------------------------------------------------------------
    | Actualiza datos personales (no contraseña — tiene ruta propia).
    | Redirige al index (no al show que ya no existe).
    */
    public function update(Request $request, User $usuario)
    {
        $request->merge([
            'nombre'         => trim($request->nombre         ?? ''),
            'apellidos'      => trim($request->apellidos      ?? ''),
            'identificacion' => trim($request->identificacion ?? ''),
            'correo'         => trim($request->correo         ?? ''),
            'ubicacion'      => trim($request->ubicacion      ?? ''),
            'contacto'       => trim($request->contacto       ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglasUpdate($usuario),
            $this->mensajes()
        );

        try {

            $validated['activo'] = $request->boolean('activo', false);

            $usuario->update($validated);

            return redirect()
                ->route('admin.usuarios.index')
                ->with('exito', "Usuario {$usuario->nombre_completo} actualizado correctamente.");

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_usuario' => 'Ocurrió un error al actualizar el usuario.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | password
    |----------------------------------------------------------------------
    */
    public function password(Request $request, User $usuario)
    {
        $request->validate(
            ['password' => ['required', 'string', 'min:8', 'confirmed']],
            [
                'password.required'  => 'La nueva contraseña es obligatoria.',
                'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
            ]
        );

        try {

            $usuario->update(['password' => Hash::make($request->password)]);

            return back()->with('exito', 'Contraseña actualizada correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_usuario' => 'No fue posible actualizar la contraseña.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | activar / desactivar
    |----------------------------------------------------------------------
    */
    public function activar(User $usuario)
    {
        try {

            $usuario->update(['activo' => true]);

            return back()->with('exito', "Usuario {$usuario->nombre_completo} activado.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_usuario' => 'No fue posible activar el usuario.']);
        }
    }

    public function desactivar(User $usuario)
    {
        try {

            $usuario->update(['activo' => false]);

            return back()->with('exito', "Usuario {$usuario->nombre_completo} desactivado.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_usuario' => 'No fue posible desactivar el usuario.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy — eliminación individual
    |----------------------------------------------------------------------
    */
    public function destroy(User $usuario)
    {
        // Pre-check: no eliminar si tiene relaciones académicas
        if ($usuario->inscripciones()->exists() || $usuario->asignaciones()->exists()) {
            return back()->withErrors([
                'error_usuario' =>
                    "No se puede eliminar a {$usuario->nombre_completo} porque tiene " .
                    "inscripciones o asignaciones registradas en el sistema.",
            ]);
        }

        try {

            $nombre = $usuario->nombre_completo;

            DB::transaction(fn () => $usuario->delete());

            return redirect()
                ->route('admin.usuarios.index')
                ->with('exito', "Usuario {$nombre} eliminado correctamente.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_usuario' => $e->getMessage()]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroyBulk — eliminación masiva (Opción B)
    |----------------------------------------------------------------------
    | Recibe un array de IDs. Antes de eliminar CUALQUIERA, verifica que
    | TODOS sean eliminables. Si alguno tiene relaciones, rechaza toda la
    | operación con un mensaje que indica cuáles no se pueden eliminar.
    | Solo procede si todos pasan el pre-check.
    */
    public function destroyBulk(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:users,id'],
        ]);

        $usuarios = User::whereIn('id', $request->ids)->get();

        // Pre-check: verificar todos antes de eliminar alguno
        $conRelaciones = $usuarios->filter(function ($u) {
            return $u->inscripciones()->exists() || $u->asignaciones()->exists();
        });

        if ($conRelaciones->isNotEmpty()) {
            $nombres = $conRelaciones->map(fn ($u) => $u->nombre_completo)->join(', ');

            return back()->withErrors([
                'error_usuario' =>
                    "No se pudo completar la eliminación. Los siguientes usuarios tienen " .
                    "inscripciones o asignaciones y no pueden eliminarse: {$nombres}.",
            ]);
        }

        try {

            $cantidad = $usuarios->count();

            DB::transaction(fn () => User::whereIn('id', $request->ids)->delete());

            return redirect()
                ->route('admin.usuarios.index')
                ->with('exito', "{$cantidad} usuario(s) eliminado(s) correctamente.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_usuario' => 'Ocurrió un error al eliminar los usuarios.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | VALIDACIONES
    |----------------------------------------------------------------------
    */
    private function reglasStore(): array
    {
        return [
            'nombre'         => ['required', 'string', 'max:255'],
            'apellidos'      => ['required', 'string', 'max:255'],
            'identificacion' => ['required', 'regex:/^[0-9]+$/', 'max:30', 'unique:users,identificacion'],
            'correo'         => ['required', 'email', 'max:255'],
            'ubicacion'      => ['nullable', 'string', 'max:150'],
            'contacto'       => ['nullable', 'regex:/^[0-9+\-\s]+$/', 'max:20'],
            'rol'            => ['required', Rule::in(['administrador', 'docente', 'estudiante'])],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'activo'         => ['nullable', 'boolean'],
        ];
    }

    private function reglasUpdate(User $usuario): array
    {
        return [
            'nombre'         => ['required', 'string', 'max:255'],
            'apellidos'      => ['required', 'string', 'max:255'],
            'identificacion' => [
                'required', 'regex:/^[0-9]+$/', 'max:30',
                Rule::unique('users', 'identificacion')->ignore($usuario->id),
            ],
            'correo'    => ['required', 'email', 'max:255'],
            'ubicacion' => ['nullable', 'string', 'max:150'],
            'contacto'  => ['nullable', 'regex:/^[0-9+\-\s]+$/', 'max:20'],
            'rol'       => ['required', Rule::in(['administrador', 'docente', 'estudiante'])],
            'activo'    => ['nullable', 'boolean'],
        ];
    }

    private function mensajes(): array
    {
        return [
            'nombre.required'          => 'El nombre es obligatorio.',
            'nombre.max'               => 'El nombre no puede superar los 255 caracteres.',
            'apellidos.required'       => 'Los apellidos son obligatorios.',
            'apellidos.max'            => 'Los apellidos no pueden superar los 255 caracteres.',
            'identificacion.required'  => 'La identificación es obligatoria.',
            'identificacion.regex'     => 'La identificación solo puede contener números.',
            'identificacion.max'       => 'La identificación no puede superar los 30 dígitos.',
            'identificacion.unique'    => 'Ya existe un usuario con esa identificación.',
            'correo.required'          => 'El correo electrónico es obligatorio.',
            'correo.email'             => 'El correo electrónico no es válido.',
            'correo.max'               => 'El correo no puede superar los 255 caracteres.',
            'contacto.regex'           => 'El contacto solo puede contener números, espacios, + o guiones.',
            'contacto.max'             => 'El contacto no puede superar los 20 caracteres.',
            'ubicacion.max'            => 'La ubicación no puede superar los 150 caracteres.',
            'rol.required'             => 'El rol es obligatorio.',
            'rol.in'                   => 'El rol seleccionado no es válido.',
            'password.required'        => 'La contraseña es obligatoria.',
            'password.min'             => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'       => 'Las contraseñas no coinciden.',
        ];
    }
}
