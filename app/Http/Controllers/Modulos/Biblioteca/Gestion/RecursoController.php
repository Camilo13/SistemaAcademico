<?php

namespace App\Http\Controllers\Modulos\Biblioteca\Gestion;

use App\Http\Controllers\Controller;
use App\Models\BibliotecaMateria;
use App\Models\Recurso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/*
|--------------------------------------------------------------------------
| RecursoController — Gestión de recursos por materia (admin)
|--------------------------------------------------------------------------
*/

class RecursoController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index(BibliotecaMateria $materia)
    {
        $recursos = $materia->recursos()
            ->orderByDesc('created_at')
            ->get();

        return view(
            'modulos.biblioteca.gestion.recurso.index',
            compact('materia', 'recursos')
        );
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create(BibliotecaMateria $materia)
    {
        return view(
            'modulos.biblioteca.gestion.recurso.create',
            compact('materia')
        );
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(Request $request, BibliotecaMateria $materia)
    {
        $request->merge([
            'titulo'      => trim($request->titulo      ?? ''),
            'descripcion' => trim($request->descripcion ?? ''),
            'url'         => trim($request->url         ?? ''),
            'autor'       => trim($request->autor       ?? ''),
        ]);

        $validated = $request->validate(
            [
                'titulo'      => ['required', 'string', 'max:255'],
                'descripcion' => ['nullable', 'string', 'max:500'],
                'tipo'        => ['required', 'in:' . implode(',', Recurso::TIPOS)],
                'metodo'      => ['required', 'in:' . implode(',', Recurso::ORIGENES)],
                'url'         => ['nullable', 'url', 'max:500'],
                'archivo'     => [
                    'nullable', 'file',
                    'max:51200', // 50 MB
                    'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mp3,jpg,jpeg,png,gif,webp',
                ],
                'autor' => ['nullable', 'string', 'max:150'],
            ],
            $this->mensajes()
        );

        // Validaciones condicionales según el método
        if ($validated['metodo'] === 'url' && empty($validated['url'])) {
            return back()
                ->withErrors(['url' => 'Debe proporcionar una URL válida para el enlace.'])
                ->withInput();
        }

        if ($validated['metodo'] === 'archivo' && !$request->hasFile('archivo')) {
            return back()
                ->withErrors(['archivo' => 'Debe seleccionar un archivo para subir.'])
                ->withInput();
        }

        try {

            DB::transaction(function () use ($request, $validated, $materia) {

                $url      = null;
                $mimeType = null;

                if ($validated['metodo'] === 'url') {
                    $url = $validated['url'];
                }

                if ($validated['metodo'] === 'archivo' && $request->hasFile('archivo')) {
                    $archivo  = $request->file('archivo');
                    $url      = $archivo->store('biblioteca/recursos', 'public');
                    $mimeType = $archivo->getMimeType();
                }

                $materia->recursos()->create([
                    'titulo'      => $validated['titulo'],
                    'descripcion' => $validated['descripcion'] ?: null,
                    'tipo'        => $validated['tipo'],
                    'origen'      => $validated['metodo'],
                    'url'         => $url,
                    'mime_type'   => $mimeType,
                    'autor'       => $validated['autor'] ?: null,
                    'visible'     => true,
                ]);
            });

            return redirect()
                ->route('admin.biblioteca.materias.recursos.index', $materia->id_materia)
                ->with('exito', 'Recurso creado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_recurso' => 'Ocurrió un error al guardar el recurso.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(BibliotecaMateria $materia, Recurso $recurso)
    {
        return view(
            'modulos.biblioteca.gestion.recurso.edit',
            compact('materia', 'recurso')
        );
    }

    /*
    |----------------------------------------------------------------------
    | update — Solo datos del recurso (título, descripción, tipo, url/archivo, autor)
    | La visibilidad se maneja con activar() / desactivar()
    |----------------------------------------------------------------------
    */
    public function update(Request $request, BibliotecaMateria $materia, Recurso $recurso)
    {
        $request->merge([
            'titulo'      => trim($request->titulo      ?? ''),
            'descripcion' => trim($request->descripcion ?? ''),
            'url'         => trim($request->url         ?? ''),
            'autor'       => trim($request->autor       ?? ''),
        ]);

        $validated = $request->validate(
            [
                'titulo'      => ['required', 'string', 'max:255'],
                'descripcion' => ['nullable', 'string', 'max:500'],
                'tipo'        => ['required', 'in:' . implode(',', Recurso::TIPOS)],
                'metodo'      => ['required', 'in:' . implode(',', Recurso::ORIGENES)],
                'url'         => ['nullable', 'url', 'max:500'],
                'archivo'     => [
                    'nullable', 'file',
                    'max:51200',
                    'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mp3,jpg,jpeg,png,gif,webp',
                ],
                'autor' => ['nullable', 'string', 'max:150'],
            ],
            $this->mensajes()
        );

        if ($validated['metodo'] === 'url' && empty($validated['url'])) {
            return back()
                ->withErrors(['url' => 'Debe proporcionar una URL válida.'])
                ->withInput();
        }

        try {

            DB::transaction(function () use ($request, $validated, $recurso) {

                $data = [
                    'titulo'      => $validated['titulo'],
                    'descripcion' => $validated['descripcion'] ?: null,
                    'tipo'        => $validated['tipo'],
                    'origen'      => $validated['metodo'],
                    'autor'       => $validated['autor'] ?: null,
                ];

                // Actualizar URL
                if ($validated['metodo'] === 'url') {
                    $data['url']       = $validated['url'];
                    $data['mime_type'] = null;
                }

                // Reemplazar archivo físico
                if ($validated['metodo'] === 'archivo' && $request->hasFile('archivo')) {

                    // Eliminar el archivo anterior si existía
                    if ($recurso->origen === 'archivo' && $recurso->url) {
                        Storage::disk('public')->delete($recurso->url);
                    }

                    $archivo          = $request->file('archivo');
                    $data['url']      = $archivo->store('biblioteca/recursos', 'public');
                    $data['mime_type'] = $archivo->getMimeType();
                }

                $recurso->update($data);
            });

            return redirect()
                ->route('admin.biblioteca.materias.recursos.index', $recurso->id_materia)
                ->with('exito', 'Recurso actualizado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_recurso' => 'Ocurrió un error al actualizar el recurso.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | activar — Hace visible el recurso
    |----------------------------------------------------------------------
    */
    public function activar(BibliotecaMateria $materia, Recurso $recurso)
    {
        try {

            DB::transaction(fn () => $recurso->update(['visible' => true]));

            return back()->with('exito', "El recurso \"{$recurso->titulo}\" ahora es visible.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_recurso' => 'No fue posible activar el recurso.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | desactivar — Oculta el recurso
    |----------------------------------------------------------------------
    */
    public function desactivar(BibliotecaMateria $materia, Recurso $recurso)
    {
        try {

            DB::transaction(fn () => $recurso->update(['visible' => false]));

            return back()->with('exito', "El recurso \"{$recurso->titulo}\" fue ocultado.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_recurso' => 'No fue posible desactivar el recurso.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(BibliotecaMateria $materia, Recurso $recurso)
    {
        try {

            $titulo    = $recurso->titulo;
            $idMateria = $recurso->id_materia;

            DB::transaction(function () use ($recurso) {

                // Eliminar archivo físico antes de borrar el registro
                if ($recurso->origen === 'archivo' && $recurso->url) {
                    Storage::disk('public')->delete($recurso->url);
                }

                $recurso->delete();
            });

            return redirect()
                ->route('admin.biblioteca.materias.recursos.index', $idMateria)
                ->with('exito', "Recurso \"{$titulo}\" eliminado correctamente.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_recurso' => $e->getMessage()]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | Mensajes de validación personalizados
    |----------------------------------------------------------------------
    */
    private function mensajes(): array
    {
        return [
            'titulo.required'      => 'El título del recurso es obligatorio.',
            'titulo.max'           => 'El título no puede superar los 255 caracteres.',
            'descripcion.max'      => 'La descripción no puede superar los 500 caracteres.',
            'tipo.required'        => 'El tipo de recurso es obligatorio.',
            'tipo.in'              => 'El tipo seleccionado no es válido.',
            'metodo.required'      => 'Debe indicar cómo se proporcionará el recurso.',
            'metodo.in'            => 'El método seleccionado no es válido.',
            'url.url'              => 'El enlace no tiene un formato de URL válido.',
            'url.max'              => 'La URL no puede superar los 500 caracteres.',
            'archivo.file'         => 'El archivo no es válido.',
            'archivo.max'          => 'El archivo no puede superar los 50 MB.',
            'archivo.mimes'        => 'El tipo de archivo no está permitido.',
            'autor.max'            => 'El autor no puede superar los 150 caracteres.',
        ];
    }
}
