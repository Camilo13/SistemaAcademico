<?php

namespace App\Http\Controllers\Modulos\Academico\Estructura;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Throwable;

class SedeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DE SEDES
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $sedes = Sede::orderBy('nombre')->get();

        return view(
            'modulos.academico.estructura.sede.index',
            compact('sedes')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO CREAR
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('modulos.academico.estructura.sede.create');
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRAR SEDE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        // ?? '' evita que trim() reciba null en PHP 8.2 (deprecation warning)
        $request->merge([
            'codigo'    => Str::upper(trim($request->codigo    ?? '')),
            'nombre'    => trim($request->nombre               ?? ''),
            'direccion' => trim($request->direccion            ?? ''),
            'telefono'  => trim($request->telefono             ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglas(),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($validated) {

                Sede::create([
                    'codigo'    => $validated['codigo']    ?: null,
                    'nombre'    => $validated['nombre'],
                    'direccion' => $validated['direccion'] ?: null,
                    'telefono'  => $validated['telefono']  ?: null,
                    'activa'    => $validated['activa']    ?? true,
                ]);
            });

            return redirect()
                ->route('admin.academico.sedes.index')
                ->with('exito', 'La sede fue creada correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_sede' => 'Ocurrió un error interno al crear la sede.'])
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO EDITAR
    |--------------------------------------------------------------------------
    */
    public function edit(Sede $sede)
    {
        return view(
            'modulos.academico.estructura.sede.edit',
            compact('sede')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR SEDE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Sede $sede)
    {
        // ?? '' evita que trim() reciba null en PHP 8.2 (deprecation warning)
        $request->merge([
            'codigo'    => Str::upper(trim($request->codigo    ?? '')),
            'nombre'    => trim($request->nombre               ?? ''),
            'direccion' => trim($request->direccion            ?? ''),
            'telefono'  => trim($request->telefono             ?? ''),
        ]);

        $validated = $request->validate(
            $this->reglas($sede->id),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($sede, $validated) {

                $sede->update([
                    'codigo'    => $validated['codigo']    ?: null,
                    'nombre'    => $validated['nombre'],
                    'direccion' => $validated['direccion'] ?: null,
                    'telefono'  => $validated['telefono']  ?: null,
                    'activa'    => $validated['activa']    ?? false,
                ]);
            });

            return redirect()
                ->route('admin.academico.sedes.index')
                ->with('exito', 'La sede fue actualizada correctamente.');

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_sede' => 'Ocurrió un error interno al actualizar la sede.'])
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVAR SEDE
    |--------------------------------------------------------------------------
    */
    public function activar(Sede $sede)
    {
        try {

            DB::transaction(fn () => $sede->activar());

            return back()->with('exito', 'La sede fue activada correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_sede' => 'No fue posible activar la sede.']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESACTIVAR SEDE
    |--------------------------------------------------------------------------
    */
    public function desactivar(Sede $sede)
    {
        try {

            DB::transaction(fn () => $sede->desactivar());

            return back()->with('exito', 'La sede fue desactivada correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_sede' => 'No fue posible desactivar la sede.']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR SEDE
    |--------------------------------------------------------------------------
    */
    public function destroy(Sede $sede)
    {
        // Pre-check 1: no eliminar sede activa
        if ($sede->activa) {
            return back()->withErrors([
                'error_sede' => 'No se puede eliminar una sede activa. Desactívala primero.',
            ]);
        }

        // Pre-check 2: no eliminar si tiene grados asociados
        if ($sede->grados()->exists()) {
            return back()->withErrors([
                'error_sede' =>
                    'No se puede eliminar la sede porque tiene grados asociados. ' .
                    'Elimina primero los grados vinculados a esta sede.',
            ]);
        }

        try {

            DB::transaction(fn () => $sede->delete());

            // Redirect al index — NO usar back() porque la sede ya no existe
            return redirect()
                ->route('admin.academico.sedes.index')
                ->with('exito', 'La sede fue eliminada correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_sede' => $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REGLAS DE VALIDACIÓN
    |--------------------------------------------------------------------------
    */
    private function reglas($id = null): array
    {
        return [
            'codigo' => [
                'nullable', 'string', 'max:20', 'alpha_dash',
                Rule::unique('sedes', 'codigo')->ignore($id),
            ],
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('sedes', 'nombre')->ignore($id),
            ],
            'direccion' => ['nullable', 'string', 'max:150'],
            'telefono'  => [
                'nullable', 'string', 'max:20',
                'regex:/^[0-9+\-\s]+$/',
            ],
            'activa' => ['nullable', 'boolean'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MENSAJES PERSONALIZADOS
    |--------------------------------------------------------------------------
    */
    private function mensajes(): array
    {
        return [
            'codigo.unique'     => 'El código ya está registrado en otra sede.',
            'codigo.alpha_dash' => 'El código solo puede contener letras, números, guiones y guion bajo.',
            'codigo.max'        => 'El código no puede superar los 20 caracteres.',
            'nombre.required'   => 'El nombre de la sede es obligatorio.',
            'nombre.max'        => 'El nombre no puede superar los 100 caracteres.',
            'nombre.unique'     => 'Ya existe una sede registrada con ese nombre.',
            'direccion.max'     => 'La dirección no puede superar los 150 caracteres.',
            'telefono.max'      => 'El teléfono no puede superar los 20 caracteres.',
            'telefono.regex'    => 'El teléfono solo puede contener números, espacios, + o guiones.',
            'activa.boolean'    => 'El estado activo no es válido.',
        ];
    }
}
