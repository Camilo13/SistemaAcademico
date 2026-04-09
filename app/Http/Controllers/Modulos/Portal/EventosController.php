<?php

namespace App\Http\Controllers\Modulos\Portal;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

/*
|--------------------------------------------------------------------------
| EventosController — Gestión de eventos institucionales (admin)
|--------------------------------------------------------------------------
| Mejoras respecto a la versión anterior:
|   - Rutas corregidas: admin.eventos.index (no modulos.portal.eventos)
|   - Vistas corregidas: modulos.portal.eventos (minúscula, no Eventos)
|   - try/catch + DB::transaction en todas las escrituras
|   - trim() + ?? '' antes de validar
|   - activar() / desactivar() separados del formulario principal
|   - update() ya no maneja 'activo' — eso va en activar/desactivar
|   - store() usa patrón hidden + checkbox para activo
|--------------------------------------------------------------------------
*/

class EventosController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $eventos = Evento::orderBy('fecha_evento')->paginate(15);

        return view('modulos.portal.eventos.index', compact('eventos'));
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create()
    {
        return view('modulos.portal.eventos.create');
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->merge([
            'titulo'      => trim($request->titulo      ?? ''),
            'descripcion' => trim($request->descripcion ?? ''),
            'lugar'       => trim($request->lugar       ?? ''),
        ]);

        $validated = $request->validate(
            [
                'titulo'       => ['required', 'string', 'max:255'],
                'descripcion'  => ['required', 'string', 'max:2000'],
                'lugar'        => ['required', 'string', 'max:255'],
                'fecha_evento' => ['required', 'date', 'after_or_equal:now'],
                'activo'       => ['nullable', 'boolean'],
            ],
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($request, $validated) {
                Evento::create([
                    'titulo'       => $validated['titulo'],
                    'descripcion'  => $validated['descripcion'],
                    'lugar'        => $validated['lugar'],
                    'fecha_evento' => $validated['fecha_evento'],
                    'activo'       => $request->boolean('activo', true),
                ]);
            });

            return redirect()
                ->route('admin.eventos.index')
                ->with('exito', 'Evento creado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_evento' => 'Ocurrió un error al crear el evento.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(Evento $evento)
    {
        return view('modulos.portal.eventos.edit', compact('evento'));
    }

    /*
    |----------------------------------------------------------------------
    | update — Solo datos (título, descripción, lugar, fecha)
    | La visibilidad se maneja con activar/desactivar
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Evento $evento)
    {
        $request->merge([
            'titulo'      => trim($request->titulo      ?? ''),
            'descripcion' => trim($request->descripcion ?? ''),
            'lugar'       => trim($request->lugar       ?? ''),
        ]);

        $validated = $request->validate(
            [
                'titulo'       => ['required', 'string', 'max:255'],
                'descripcion'  => ['required', 'string', 'max:2000'],
                'lugar'        => ['required', 'string', 'max:255'],
                'fecha_evento' => [
                    'required', 'date',
                    // Solo validar fecha futura si el admin la modifica
                    Rule::when(
                        $request->fecha_evento !== $evento->fecha_evento->format('Y-m-d\TH:i'),
                        ['after_or_equal:now']
                    ),
                ],
            ],
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($validated, $evento) {
                $evento->update([
                    'titulo'       => $validated['titulo'],
                    'descripcion'  => $validated['descripcion'],
                    'lugar'        => $validated['lugar'],
                    'fecha_evento' => $validated['fecha_evento'],
                ]);
            });

            return redirect()
                ->route('admin.eventos.index')
                ->with('exito', 'Evento actualizado correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_evento' => 'Ocurrió un error al actualizar el evento.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | activar
    |----------------------------------------------------------------------
    */
    public function activar(Evento $evento)
    {
        try {

            DB::transaction(fn () => $evento->update(['activo' => true]));

            return back()->with('exito', "El evento \"{$evento->titulo}\" fue activado.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_evento' => 'No fue posible activar el evento.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | desactivar
    |----------------------------------------------------------------------
    */
    public function desactivar(Evento $evento)
    {
        try {

            DB::transaction(fn () => $evento->update(['activo' => false]));

            return back()->with('exito', "El evento \"{$evento->titulo}\" fue desactivado.");

        } catch (Throwable $e) {

            return back()->withErrors(['error_evento' => 'No fue posible desactivar el evento.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(Evento $evento)
    {
        try {

            $titulo = $evento->titulo;

            DB::transaction(fn () => $evento->delete());

            return redirect()
                ->route('admin.eventos.index')
                ->with('exito', "Evento \"{$titulo}\" eliminado correctamente.");

        } catch (Throwable $e) {

            return back()->withErrors([
                'error_evento' => 'Ocurrió un error al eliminar el evento.',
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
            'titulo.required'             => 'El título del evento es obligatorio.',
            'titulo.max'                  => 'El título no puede superar los 255 caracteres.',
            'descripcion.required'        => 'La descripción es obligatoria.',
            'descripcion.max'             => 'La descripción no puede superar los 2000 caracteres.',
            'lugar.required'              => 'El lugar del evento es obligatorio.',
            'lugar.max'                   => 'El lugar no puede superar los 255 caracteres.',
            'fecha_evento.required'       => 'La fecha y hora del evento son obligatorias.',
            'fecha_evento.date'           => 'Debe ingresar una fecha válida.',
            'fecha_evento.after_or_equal' => 'La fecha del evento no puede ser en el pasado.',
        ];
    }
}
