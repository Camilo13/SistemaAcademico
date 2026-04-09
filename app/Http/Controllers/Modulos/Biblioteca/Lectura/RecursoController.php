<?php

namespace App\Http\Controllers\Modulos\Biblioteca\Lectura;

use App\Http\Controllers\Controller;
use App\Models\BibliotecaMateria;

/*
|--------------------------------------------------------------------------
| RecursoController — Lectura de recursos por materia (docente + estudiante)
|--------------------------------------------------------------------------
| Solo lectura — sin CRUD.
| Muestra únicamente los recursos visibles de la materia.
| La propiedad url_final ya es un accessor del modelo Recurso,
| por lo que no es necesario el .map() de la versión anterior.
|--------------------------------------------------------------------------
*/

class RecursoController extends Controller
{
    public function index(BibliotecaMateria $materia)
    {
        // Verificar que la materia sea visible para lectura
        if (!$materia->visible) {
            abort(404);
        }

        $user = auth()->user();

        $recursos = $materia->recursos()
            ->where('visible', true)
            ->orderByDesc('created_at')
            ->get();

        $layout = match ($user->rol) {
            'docente'    => 'layouts.menudocente',
            'estudiante' => 'layouts.menuestudiante',
            default      => abort(403),
        };

        return view(
            'modulos.biblioteca.lectura.recurso.index',
            compact('materia', 'recursos', 'layout')
        );
    }
}
