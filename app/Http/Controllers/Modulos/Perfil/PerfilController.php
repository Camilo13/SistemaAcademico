<?php

namespace App\Http\Controllers\Modulos\Perfil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
