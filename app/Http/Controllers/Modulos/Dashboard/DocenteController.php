<?php

namespace App\Http\Controllers\Modulos\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Controlador del Docente
|--------------------------------------------------------------------------
*/

class DocenteController extends Controller
{
    public function index()
    {
        return view('dashboard.docente');
    }
}
