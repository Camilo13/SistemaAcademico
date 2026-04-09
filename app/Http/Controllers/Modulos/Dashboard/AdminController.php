<?php

namespace App\Http\Controllers\Modulos\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Controlador del Administrador
|--------------------------------------------------------------------------
| Gestiona las vistas y acciones exclusivas del rol administrador
*/

class AdminController extends Controller
{
    /**
     * Muestra el panel principal del administrador
     */
    public function index()
    {
        return view('dashboard.admin');
    }
}
