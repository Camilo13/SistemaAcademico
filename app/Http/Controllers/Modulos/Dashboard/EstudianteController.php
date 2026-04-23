<?php

namespace App\Http\Controllers\Modulos\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EstudianteController extends Controller
{
    public function index()
    {
        return view('dashboard.estudiante', [
            'usuario' => Auth::user(),
        ]);
    }
}
