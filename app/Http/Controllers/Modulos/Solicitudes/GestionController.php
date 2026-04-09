<?php

namespace App\Http\Controllers\Modulos\Solicitudes;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/*
|--------------------------------------------------------------------------
| GestionController — Solicitudes de registro (panel admin)
|--------------------------------------------------------------------------
| Cambios respecto a la versión anterior:
|   - show() eliminado — toda la info visible desde el index en las tarjetas
|   - update() redirige a index (no a show)
|   - edit accesible directo desde el index
|   - trim() + ?? '' en update()
|   - try/catch en rechazar()
|   - Mensajes personalizados en las validaciones
|
| Rutas activas:
|   GET  /admin/solicitudes                    → admin.solicitudes.index
|   GET  /admin/solicitudes/{s}/edit           → admin.solicitudes.edit
|   PUT  /admin/solicitudes/{s}                → admin.solicitudes.update
|   POST /admin/solicitudes/{s}/aprobar        → admin.solicitudes.aprobar
|   POST /admin/solicitudes/{s}/rechazar       → admin.solicitudes.rechazar
|--------------------------------------------------------------------------
*/

class GestionController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index — Listado de solicitudes pendientes
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $solicitudes = Solicitud::pendientes()
            ->orderByDesc('created_at')
            ->get();

        return view('modulos.solicitudes.gestion.index', compact('solicitudes'));
    }

    /*
    |----------------------------------------------------------------------
    | edit — Formulario de edición (solo pendientes)
    |----------------------------------------------------------------------
    */
    public function edit(Solicitud $solicitud)
    {
        if ($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
            return redirect()
                ->route('admin.solicitudes.index')
                ->with('advertencia', 'Solo se pueden editar solicitudes pendientes.');
        }

        return view('modulos.solicitudes.gestion.edit', compact('solicitud'));
    }

    /*
    |----------------------------------------------------------------------
    | update — Guardar cambios en la solicitud
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Solicitud $solicitud)
    {
        if ($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
            return redirect()
                ->route('admin.solicitudes.index')
                ->with('advertencia', 'Esta solicitud ya fue procesada.');
        }

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
            [
                'nombre'         => ['required', 'string', 'max:255'],
                'apellidos'      => ['required', 'string', 'max:255'],
                'identificacion' => [
                    'required',
                    'regex:/^[0-9]+$/',
                    'unique:solicitudes,identificacion,' . $solicitud->id,
                    'unique:users,identificacion',
                ],
                'rol'       => ['required', 'in:docente,estudiante'],
                'correo'    => ['required', 'email', 'max:255'],
                'ubicacion' => ['required', 'string', 'max:150'],
                'contacto'  => ['required', 'regex:/^[0-9]+$/', 'max:20'],
            ],
            [
                'nombre.required'         => 'El nombre es obligatorio.',
                'apellidos.required'      => 'Los apellidos son obligatorios.',
                'identificacion.required' => 'La identificación es obligatoria.',
                'identificacion.regex'    => 'La identificación solo puede contener números.',
                'identificacion.unique'   => 'Ya existe una solicitud o cuenta con esa identificación.',
                'rol.required'            => 'El rol es obligatorio.',
                'rol.in'                  => 'El rol debe ser docente o estudiante.',
                'correo.required'         => 'El correo es obligatorio.',
                'correo.email'            => 'El correo no tiene un formato válido.',
                'ubicacion.required'      => 'La ubicación es obligatoria.',
                'contacto.required'       => 'El contacto es obligatorio.',
                'contacto.regex'          => 'El contacto solo puede contener números.',
            ]
        );

        try {

            $solicitud->update($validated);

            // Redirige al index — show fue eliminado
            return redirect()
                ->route('admin.solicitudes.index')
                ->with('exito', 'Solicitud actualizada correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_solicitud' => 'Error al actualizar la solicitud.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | aprobar — Crear usuario y marcar como aprobada
    |----------------------------------------------------------------------
    */
    public function aprobar(Solicitud $solicitud)
    {
        if ($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
            return back()->with('advertencia', 'Esta solicitud ya fue procesada.');
        }

        try {

            DB::transaction(function () use ($solicitud) {

                if (User::where('identificacion', $solicitud->identificacion)->exists()) {
                    throw new \Exception(
                        'Ya existe un usuario con la identificación ' .
                        $solicitud->identificacion . ' en el sistema.'
                    );
                }

                User::create([
                    'identificacion' => $solicitud->identificacion,
                    'nombre'         => $solicitud->nombre,
                    'apellidos'      => $solicitud->apellidos,
                    'correo'         => $solicitud->correo,
                    'ubicacion'      => $solicitud->ubicacion,
                    'contacto'       => $solicitud->contacto,
                    'rol'            => $solicitud->rol,
                    'activo'         => true,
                    'password'       => $solicitud->password,
                ]);

                $solicitud->update(['estado' => Solicitud::ESTADO_APROBADA]);
            });

            return redirect()
                ->route('admin.solicitudes.index')
                ->with('exito', 'Solicitud aprobada. Usuario creado correctamente.');

        } catch (Throwable $e) {

            Log::error('Error al aprobar solicitud', [
                'solicitud_id' => $solicitud->id,
                'error'        => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error_solicitud' => $e->getMessage(),
            ]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | rechazar — Marcar como rechazada
    |----------------------------------------------------------------------
    */
    public function rechazar(Solicitud $solicitud)
    {
        if ($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
            return back()->with('advertencia', 'Esta solicitud ya fue procesada.');
        }

        try {

            $solicitud->update(['estado' => Solicitud::ESTADO_RECHAZADA]);

            return redirect()
                ->route('admin.solicitudes.index')
                ->with('info', 'La solicitud fue rechazada.');

        } catch (Throwable $e) {

            return back()->withErrors([
                'error_solicitud' => 'No fue posible rechazar la solicitud.',
            ]);
        }
    }
}
