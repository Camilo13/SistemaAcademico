<?php

namespace App\Http\Controllers\Modulos\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Controlador del Estudiante
|--------------------------------------------------------------------------
*/

class EstudianteController extends Controller
{
    public function index()
    {
        return view('dashboard.estudiante');
    }
}
