<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarRol
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            abort(401, 'Usuario no autenticado');
        }

        if (!in_array(auth()->user()->rol, $roles)) {
            abort(403, 'Acceso no autorizado');
        }

        return $next($request);
    }
}
