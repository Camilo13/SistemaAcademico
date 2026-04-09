<?php

namespace App\Http\Controllers\Modulos\Solicitudes;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SolicitudController extends Controller
{
    public function create()
    {
        return view('modulos.solicitudes.solicitud');
    }

    // Validación AJAX
    public function validarCampo(Request $request)
    {
        if ($request->filled('identificacion')) {

            $existe = Solicitud::where('identificacion', $request->identificacion)->exists()
                || User::where('identificacion', $request->identificacion)->exists();

            return response()->json([
                'valido'  => !$existe,
                'mensaje' => $existe
                    ? 'Ya existe una solicitud o cuenta con esta identificación.'
                    : null
            ]);
        }

        return response()->json(['valido' => true]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],

            'identificacion' => [
                'required',
                'regex:/^[0-9]+$/',
                'unique:solicitudes,identificacion',
                'unique:users,identificacion',
            ],

            'rol' => ['required', 'in:docente,estudiante'],

            'correo'    => ['required', 'email'],
            'ubicacion' => ['required', 'string', 'max:150'],
            'contacto'  => ['required', 'regex:/^[0-9]+$/', 'max:20'],

            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        Solicitud::create([
            'identificacion' => $validated['identificacion'],
            'nombre'         => $validated['nombre'],
            'apellidos'      => $validated['apellidos'],
            'correo'         => $validated['correo'],
            'ubicacion'      => $validated['ubicacion'],
            'contacto'       => $validated['contacto'],
            'rol'            => $validated['rol'],
            'estado'         => Solicitud::ESTADO_PENDIENTE,
            'password'       => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('login')
            ->with('exito', 'Tu solicitud fue enviada correctamente. El administrador la revisará.');
    }
}