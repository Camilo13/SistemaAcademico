<?php

namespace App\Http\Controllers\Modulos\Biblioteca\Gestion;

use App\Http\Controllers\Controller;
use App\Models\BibliotecaMateria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/*
|--------------------------------------------------------------------------
| MateriaController — Gestión de materias de biblioteca (admin)
|--------------------------------------------------------------------------
*/

class MateriaController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $materias = BibliotecaMateria::orderBy('nombre')->get();

        return view('modulos.biblioteca.gestion.materia.index', compact('materias'));
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create()
    {
        return view('modulos.biblioteca.gestion.materia.create');
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->merge([
            'nombre'      => trim($request->nombre      ?? ''),
            'descripcion' => trim($request->descripcion ?? ''),
        ]);

        $validated = $request->validate(
            [
                'nombre'      => ['required', 'string', 'max:150', 'unique:bibliotecamateria,nombre'],
                'descripcion' => ['nullable', 'string', 'max:500'],
            ],
            [
                'nombre.required' => 'El nombre de la materia es obligatorio.',
                'nombre.max'      => 'El nombre no puede superar los 150 caracteres.',
                'nombre.unique'   => 'Ya existe una materia con ese nombre.',
                'descripcion.max' => 'La descripción no puede superar los 500 caracteres.',
            ]
        );

        try {

            DB::transaction(function () use ($validated) {
                BibliotecaMateria::create([
                    'nombre'      => $validated['nombre'],
                    'descripcion' => $validated['descripcion'] ?: null,
                    'visible'     => true,
                ]);
            });

            return redirect()
                ->route('admin.biblioteca.materias.index')
                ->with('exito', 'Materia creada correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_materia' => 'Ocurrió un error al crear la materia.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(BibliotecaMateria $materia)
    {
        return view('modulos.biblioteca.gestion.materia.edit', compact('materia'));
    }

    /*
    |----------------------------------------------------------------------
    | update — Solo datos (nombre y descripción)
    | La visibilidad se maneja con activar/desactivar
    |----------------------------------------------------------------------
    */
    public function update(Request $request, BibliotecaMateria $materia)
    {
        $request->merge([
            'nombre'      => trim($request->nombre      ?? ''),
            'descripcion' => trim($request->descripcion ?? ''),
        ]);

        $validated = $request->validate(
            [
                'nombre' => [
                    'required', 'string', 'max:150',
                    \Illuminate\Validation\Rule::unique('bibliotecamateria', 'nombre')
                        ->ignore($materia->id_materia, 'id_materia'),
                ],
                'descripcion' => ['nullable', 'string', 'max:500'],
            ],
            [
                'nombre.required' => 'El nombre de la materia es obligatorio.',
                'nombre.max'      => 'El nombre no puede superar los 150 caracteres.',
                'nombre.unique'   => 'Ya existe otra materia con ese nombre.',
                'descripcion.max' => 'La descripción no puede superar los 500 caracteres.',
            ]
        );

        try {

            DB::transaction(function () use ($materia, $validated) {
                $materia->update([
                    'nombre'      => $validated['nombre'],
                    'descripcion' => $validated['descripcion'] ?: null,
                ]);
            });

            return redirect()
                ->route('admin.biblioteca.materias.index')
                ->with('exito', 'Materia actualizada correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_materia' => 'Ocurrió un error al actualizar la materia.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | activar — Hace visible la materia
    |----------------------------------------------------------------------
    */
    public function activar(BibliotecaMateria $materia)
    {
        try {

            DB::transaction(fn () => $materia->mostrar());

            return back()->with('exito', "La materia \"{$materia->nombre}\" ahora es visible.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_materia' => 'No fue posible activar la materia.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | desactivar — Oculta la materia
    |----------------------------------------------------------------------
    */
    public function desactivar(BibliotecaMateria $materia)
    {
        try {

            DB::transaction(fn () => $materia->ocultar());

            return back()->with('exito', "La materia \"{$materia->nombre}\" fue ocultada.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_materia' => 'No fue posible desactivar la materia.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy — Pre-check: no eliminar si tiene recursos
    |----------------------------------------------------------------------
    */
    public function destroy(BibliotecaMateria $materia)
    {
        // Pre-check: no eliminar si tiene recursos asociados
        if ($materia->recursos()->exists()) {
            return back()->withErrors([
                'error_materia' =>
                    "No se puede eliminar la materia \"{$materia->nombre}\" porque " .
                    "tiene recursos asociados. Elimina primero los recursos de esta materia.",
            ]);
        }

        try {

            $nombre = $materia->nombre;

            DB::transaction(fn () => $materia->delete());

            return redirect()
                ->route('admin.biblioteca.materias.index')
                ->with('exito', "Materia \"{$nombre}\" eliminada correctamente.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_materia' => $e->getMessage()]);
        }
    }
}
