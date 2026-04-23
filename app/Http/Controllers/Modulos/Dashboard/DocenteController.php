<?php

namespace App\Http\Controllers\Modulos\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DocenteController extends Controller
{
    public function index()
    {
        return view('dashboard.docente', [
            'usuario' => Auth::user(),
        ]);
    }
}
