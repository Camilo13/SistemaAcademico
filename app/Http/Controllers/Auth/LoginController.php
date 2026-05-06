<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        /* informa al usuario cuando fue redirigido por timeout de inactividad */
        if (request()->query('sesion') === 'expirada') {
            session()->flash('info', 'Tu sesión expiró por inactividad. Por favor inicia sesión nuevamente.');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identificacion' => ['required'],
            'password'       => ['required'],
        ]);

        /**
         * Intento normal de autenticación
         */
        if (Auth::attempt([
            'identificacion' => $request->identificacion,
            'password'       => $request->password,
        ])) {

            $request->session()->regenerate();

            /* invalida todas las sesiones anteriores del usuario en otros dispositivos */
            Auth::logoutOtherDevices($request->password);

            return match (Auth::user()->rol) {
                'administrador' => redirect()->route('admin.dashboard'),
                'docente'       => redirect()->route('docente.dashboard'),
                'estudiante'    => redirect()->route('estudiante.dashboard'),
                default         => abort(403),
            };
        }

        /**
         *  No autenticó → revisar solicitud
         */
        $solicitud = Solicitud::where('identificacion', $request->identificacion)
            ->latest()
            ->first();

        if ($solicitud) {
            return match ($solicitud->estado) {
                Solicitud::ESTADO_PENDIENTE =>
                    back()
                        ->with('info', 'Tu solicitud se encuentra en seguimiento y aún no ha sido aprobada.')
                        ->onlyInput('identificacion'),

                Solicitud::ESTADO_RECHAZADA =>
                    back()
                        ->with('error', 'Tu solicitud fue rechazada. Comunícate con la institución.')
                        ->onlyInput('identificacion'),

                default =>
                    back()
                        ->with('error', 'No es posible iniciar sesión en este momento.')
                        ->onlyInput('identificacion'),
            };
        }

        /**
         *  No existe usuario ni solicitud
         */
        return back()
            ->with('error', 'Credenciales incorrectas.')
            ->onlyInput('identificacion');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }
}
