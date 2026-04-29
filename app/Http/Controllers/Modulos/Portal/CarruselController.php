<?php

namespace App\Http\Controllers\Modulos\Portal;

use App\Http\Controllers\Controller;
use App\Models\CarruselInicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

/*
|--------------------------------------------------------------------------
| CarruselController — Gestión del carrusel del portal (admin)
|--------------------------------------------------------------------------
| Mejoras respecto a la versión anterior:
|   - try/catch + DB::transaction en todas las escrituras
|   - activar() / desactivar() separados del formulario
|   - update() ya no maneja 'activo' — eso va en activar/desactivar
|   - destroy() elimina el archivo físico dentro de la transacción
|   - store() usa el patrón hidden + checkbox para activo
|--------------------------------------------------------------------------
*/

class CarruselController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $imagenes = CarruselInicio::orderBy('orden')->get();

        return view('modulos.portal.carrusel.index', compact('imagenes'));
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create()
    {
        return view('modulos.portal.carrusel.create');
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'imagen' => [
                    'required', 'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],
                'orden' => [
                    'nullable', 'integer', 'min:0',
                    Rule::unique('carrusel_inicios', 'orden'),
                ],
                'activo' => ['nullable', 'boolean'],
            ],
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($request, $validated) {

                $filename = 'carrusel/inicio/' . uniqid() . '.jpg';

                $request->file('imagen')->storeAs('carrusel/inicio', basename($filename), 'public');

                CarruselInicio::create([
                    'imagen' => $filename,
                    'orden'  => $validated['orden'] ?? 0,
                    'activo' => $request->boolean('activo', true),
                ]);
            });

            return redirect()
                ->route('admin.carrusel.index')
                ->with('exito', 'Imagen agregada al carrusel correctamente.');

        } catch (Throwable $e) {

            \Log::error('CarruselController@store error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());

            return back()
                ->withErrors(['error_carrusel' => 'Ocurrió un error al guardar la imagen.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(CarruselInicio $carrusel)
    {
        return view('modulos.portal.carrusel.edit', compact('carrusel'));
    }

    /*
    |----------------------------------------------------------------------
    | update — Solo datos (imagen y orden)
    | La visibilidad se maneja con activar/desactivar
    |----------------------------------------------------------------------
    */
    public function update(Request $request, CarruselInicio $carrusel)
    {
        $validated = $request->validate(
            [
                'imagen' => [
                    'nullable', 'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],
                'orden' => [
                    'nullable', 'integer', 'min:0',
                    Rule::unique('carrusel_inicios', 'orden')
                        ->ignore($carrusel->id),
                ],
            ],
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($request, $validated, $carrusel) {

                $data = [
                    'orden' => $validated['orden'] ?? $carrusel->orden,
                ];

                // Reemplazar imagen si se sube una nueva
                if ($request->hasFile('imagen')) {

                    // Eliminar imagen anterior
                    if ($carrusel->imagen &&
                        Storage::disk('public')->exists($carrusel->imagen)) {
                        Storage::disk('public')->delete($carrusel->imagen);
                    }

                    $filename = 'carrusel/inicio/' . uniqid() . '.jpg';

                    $request->file('imagen')->storeAs('carrusel/inicio', basename($filename), 'public');

                    $data['imagen'] = $filename;
                }

                $carrusel->update($data);
            });

            return redirect()
                ->route('admin.carrusel.index')
                ->with('exito', 'Imagen actualizada correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_carrusel' => 'Ocurrió un error al actualizar la imagen.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | activar
    |----------------------------------------------------------------------
    */
    public function activar(CarruselInicio $carrusel)
    {
        try {

            DB::transaction(fn () => $carrusel->update(['activo' => true]));

            return back()->with('exito', 'Imagen activada correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_carrusel' => 'No fue posible activar la imagen.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | desactivar
    |----------------------------------------------------------------------
    */
    public function desactivar(CarruselInicio $carrusel)
    {
        try {

            DB::transaction(fn () => $carrusel->update(['activo' => false]));

            return back()->with('exito', 'Imagen desactivada correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_carrusel' => 'No fue posible desactivar la imagen.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(CarruselInicio $carrusel)
    {
        $rutaImagen = $carrusel->imagen;

        try {

            // Primero confirmar el delete en BD; si falla, el archivo no se toca
            DB::transaction(fn () => $carrusel->delete());

            // Solo borrar el archivo físico tras confirmar la transacción
            if ($rutaImagen && Storage::disk('public')->exists($rutaImagen)) {
                Storage::disk('public')->delete($rutaImagen);
            }

            return redirect()
                ->route('admin.carrusel.index')
                ->with('exito', 'Imagen eliminada del carrusel correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors([
                'error_carrusel' => 'Ocurrió un error al eliminar la imagen.',
            ]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | Mensajes de validación
    |----------------------------------------------------------------------
    */
    private function mensajes(): array
    {
        return [
            'imagen.required' => 'Debes seleccionar una imagen.',
            'imagen.image'    => 'El archivo no es una imagen válida.',
            'imagen.mimes'    => 'Formatos permitidos: JPG, JPEG, PNG, WEBP.',
            'imagen.max'      => 'La imagen no puede superar los 2 MB.',
            'orden.integer'   => 'El orden debe ser un número entero.',
            'orden.min'       => 'El orden no puede ser negativo.',
            'orden.unique'    => 'Ya existe una imagen con ese número de orden.',
        ];
    }
}
