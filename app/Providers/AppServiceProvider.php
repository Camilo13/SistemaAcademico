<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

/*
|--------------------------------------------------------------------------
| Proveedor de servicios de rutas
|--------------------------------------------------------------------------
| Define la lógica de redirección después del login
*/

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Redirección después de la autenticación
     */
    public static function home(): string
    {
        // Verifica que el usuario esté autenticado
    }
}
