<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CarruselInicio;
use App\Models\Evento;

class PublicController extends Controller
{
    public function inicio()
    {
        $imagenesCarrusel = CarruselInicio::where('activo', true)
            ->orderBy('orden')
            ->get();

        return view('publico.inicio', compact('imagenesCarrusel'));
    }

    public function sobreNosotros()
    {
        return view('publico.sobrenosotros');
    }

    public function contacto()
    {
        return view('publico.contacto');
    }

    public function eventos()
    {
        $eventos = Evento::where('activo', true)
            ->where('fecha_evento', '>=', now())
            ->orderBy('fecha_evento')
            ->get();

        return view('publico.eventos', compact('eventos'));
    }
}