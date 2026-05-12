<?php

namespace App\Http\Controllers\Modulos\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ConfiguracionController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index — muestra el formulario de configuración
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $config = Configuracion::pluck('valor', 'clave');

        return view('modulos.admin.configuracion.index', compact('config'));
    }

    /*
    |----------------------------------------------------------------------
    | update — guarda los campos de texto
    |----------------------------------------------------------------------
    */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nombre_institucion' => ['nullable', 'string', 'max:255'],
            'nit_institucion'    => ['nullable', 'string', 'max:50'],
            'municipio'          => ['nullable', 'string', 'max:100'],
            'departamento'       => ['nullable', 'string', 'max:100'],
            'resolucion'         => ['nullable', 'string', 'max:255'],
        ], [
            'nombre_institucion.max' => 'El nombre no puede superar 255 caracteres.',
            'nit_institucion.max'    => 'El NIT no puede superar 50 caracteres.',
            'municipio.max'          => 'El municipio no puede superar 100 caracteres.',
            'departamento.max'       => 'El departamento no puede superar 100 caracteres.',
            'resolucion.max'         => 'La resolución no puede superar 255 caracteres.',
        ]);

        try {

            foreach ($validated as $clave => $valor) {
                Configuracion::updateOrCreate(
                    ['clave' => $clave],
                    ['valor' => $valor ?? '']
                );
            }

            return redirect()
                ->route('admin.configuracion.index')
                ->with('exito', 'Configuración guardada correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_config' => 'Ocurrió un error al guardar la configuración.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | firmaUpdate — sube la firma del rector
    |----------------------------------------------------------------------
    */
    public function firmaUpdate(Request $request)
    {
        $request->validate([
            'firma_rector' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ], [
            'firma_rector.required' => 'Debe seleccionar una imagen de firma.',
            'firma_rector.image'    => 'El archivo debe ser una imagen.',
            'firma_rector.mimes'    => 'Solo se permiten imágenes PNG o JPG.',
            'firma_rector.max'      => 'La imagen no puede superar 2 MB.',
        ]);

        try {

            // Eliminar firma anterior si existe
            $firmaActual = Configuracion::obtener(Configuracion::FIRMA_RECTOR);
            if ($firmaActual && Storage::disk('public')->exists($firmaActual)) {
                Storage::disk('public')->delete($firmaActual);
            }

            // Guardar nueva firma
            $ruta = $request->file('firma_rector')->store('firmas', 'public');

            Configuracion::updateOrCreate(
                ['clave' => Configuracion::FIRMA_RECTOR],
                ['valor' => $ruta]
            );

            return redirect()
                ->route('admin.configuracion.index')
                ->with('exito', 'Firma del rector actualizada correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_config' => 'Ocurrió un error al guardar la firma.'])
                ->withInput();
        }
    }
}
