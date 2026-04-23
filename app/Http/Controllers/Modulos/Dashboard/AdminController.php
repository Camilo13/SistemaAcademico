<?php

namespace App\Http\Controllers\Modulos\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        return view('dashboard.admin', [
            'usuario' => Auth::user(),
        ]);
    }
}
