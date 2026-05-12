<?php

namespace App\Http\Controllers\Modulos\Perfil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    /**
     * Muestra el perfil del usuario autenticado.
     *
     * Seguridad:
     * - No recibe IDs por URL
     * - No permite acceso a otros perfiles
     * - Determina el layout según el rol
     */
    public function perfil()
    {
        $user = auth()->user();

        // Determinamos el layout según el rol
        $layout = match ($user->rol) {
            'administrador' => 'layouts.menuadmin',
            'docente'       => 'layouts.menudocente',
            'estudiante'    => 'layouts.menuestudiante',
        };

        return view('modulos.Perfil.Perfil', compact('user', 'layout'));
    }

    /**
     * IMPORTANTE:
     * - NO permite cambiar contraseña
     * - NO permite cambiar rol, correo, identificación o estado
     * - Evita mass assignment
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        /**
         * Validación estricta:
         * Solo campos permitidos por la documentación
         */
        $validated = $request->validate([
            'contacto'  => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
            'ubicacion' => ['nullable', 'string', 'max:150'],
        ]);

        /**
         * Asignación controlada campo por campo
         * (protección contra inyección de campos ocultos)
         */
        $user->contacto  = $validated['contacto'] ?? null;
        $user->ubicacion = $validated['ubicacion'] ?? null;

        $user->save();

        return redirect()
            ->route('perfil')
            ->with('exito', 'Información del perfil actualizada correctamente.');
    }

    /**
     * Actualiza la firma del usuario autenticado.
     * Solo disponible para docentes y administrador.
     */
    public function firmaUpdate(Request $request)
    {
        $user = auth()->user();

        if ($user->esEstudiante()) {
            abort(403);
        }

        $request->validate([
            'firma' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ], [
            'firma.required' => 'Debe seleccionar una imagen de firma.',
            'firma.image'    => 'El archivo debe ser una imagen.',
            'firma.mimes'    => 'Solo se permiten imágenes PNG o JPG.',
            'firma.max'      => 'La imagen no puede superar 2 MB.',
        ]);

        // Eliminar firma anterior si existe
        if ($user->firma && Storage::disk('public')->exists($user->firma)) {
            Storage::disk('public')->delete($user->firma);
        }

        // Guardar nueva firma
        $ruta = $request->file('firma')->store('firmas', 'public');

        $user->firma = $ruta;
        $user->save();

        return redirect()
            ->route('perfil')
            ->with('exito', 'Firma actualizada correctamente.');
    }
}
