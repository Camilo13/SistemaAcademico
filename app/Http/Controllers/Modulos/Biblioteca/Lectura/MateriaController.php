<?php

namespace App\Http\Controllers\Modulos\Biblioteca\Lectura;

use App\Http\Controllers\Controller;
use App\Models\BibliotecaMateria;

/*
|--------------------------------------------------------------------------
| MateriaController — Lectura de materias (docente + estudiante)
|--------------------------------------------------------------------------
| Solo lectura — sin CRUD.
| Muestra únicamente las materias visibles.
| Determina el layout según el rol del usuario autenticado.
|
| Mejoras:
|   - Usa el scope ->visibles() del modelo en lugar de where('visible', true)
|--------------------------------------------------------------------------
*/

class MateriaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $materias = BibliotecaMateria::visibles()
            ->orderBy('nombre')
            ->get();

        $layout = match ($user->rol) {
            'docente'    => 'layouts.menudocente',
            'estudiante' => 'layouts.menuestudiante',
            default      => abort(403),
        };

        return view(
            'modulos.biblioteca.lectura.materia.index',
            compact('materias', 'layout')
        );
    }
}
