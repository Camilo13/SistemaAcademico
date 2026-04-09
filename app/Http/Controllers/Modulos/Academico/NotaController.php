<?php

namespace App\Http\Controllers\Modulos\Academico;

use App\Http\Controllers\Controller;
use App\Models\InscripcionMateria;
use App\Models\Nota;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class NotaController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index — Notas de una materia inscrita
    |----------------------------------------------------------------------
    */
    public function index(InscripcionMateria $inscripcionMateria)
    {
        $notas = $inscripcionMateria->notas()
            ->with('periodo')
            ->orderBy('periodo_id')
            ->get();

        return view(
            'modulos.academico.nota.index',
            compact('inscripcionMateria', 'notas')
        );
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create(InscripcionMateria $inscripcionMateria)
    {
        $anioLectivoId = $inscripcionMateria
            ->inscripcion
            ->grupo
            ->anio_lectivo_id;

        $periodos = Periodo::where('anio_lectivo_id', $anioLectivoId)
            ->orderBy('numero')
            ->get();

        return view(
            'modulos.academico.nota.create',
            compact('inscripcionMateria', 'periodos')
        );
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(Request $request, InscripcionMateria $inscripcionMateria)
    {
        $validated = $request->validate([
            'inscripcion_materia_id' => ['required', 'exists:inscripcion_materias,id'],
            'periodo_id'             => ['required', 'exists:periodos,id'],
            'nota'                   => ['required', 'numeric', 'min:0', 'max:5'],
            'observacion'            => ['nullable', 'string', 'max:1000'],
        ], [
            'inscripcion_materia_id.required' => 'Referencia de materia inválida.',
            'periodo_id.required'             => 'Debe seleccionar un periodo.',
            'nota.required'                   => 'La nota es obligatoria.',
            'nota.numeric'                    => 'La nota debe ser un número.',
            'nota.min'                        => 'La nota mínima es 0.00.',
            'nota.max'                        => 'La nota máxima es 5.00.',
        ]);

        try {

            DB::transaction(function () use ($validated) {

                $existe = Nota::where('inscripcion_materia_id', $validated['inscripcion_materia_id'])
                    ->where('periodo_id', $validated['periodo_id'])
                    ->exists();

                if ($existe) {
                    throw new RuntimeException(
                        'Ya existe una nota registrada para este periodo.'
                    );
                }

                Nota::create($validated);
            });

            return redirect()
                ->route('admin.academico.notas.index', $validated['inscripcion_materia_id'])
                ->with('exito', 'Nota registrada correctamente.');

        } catch (RuntimeException $e) {

            return back()
                ->withErrors(['error_academico' => $e->getMessage()])
                ->withInput();

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_academico' => 'Ocurrió un error al registrar la nota.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(Nota $nota)
    {
        return view(
            'modulos.academico.nota.edit',
            compact('nota')
        );
    }

    /*
    |----------------------------------------------------------------------
    | update
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Nota $nota)
    {
        $validated = $request->validate([
            'nota'        => ['required', 'numeric', 'min:0', 'max:5'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ], [
            'nota.required' => 'La nota es obligatoria.',
            'nota.numeric'  => 'La nota debe ser un número.',
            'nota.min'      => 'La nota mínima es 0.00.',
            'nota.max'      => 'La nota máxima es 5.00.',
        ]);

        try {

            DB::transaction(function () use ($nota, $validated) {
                $nota->update($validated);
            });

            return redirect()
                ->route('admin.academico.notas.index', $nota->inscripcion_materia_id)
                ->with('exito', 'Nota actualizada correctamente.');

        } catch (RuntimeException $e) {

            return back()
                ->withErrors(['error_academico' => $e->getMessage()])
                ->withInput();

        } catch (Throwable $e) {

            return back()
                ->withErrors(['error_academico' => 'Ocurrió un error al actualizar la nota.'])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(Nota $nota)
    {
        // Pre-check: no eliminar si el periodo está cerrado
        if ($nota->periodo && $nota->periodo->estaCerrado()) {
            return back()->withErrors([
                'error_academico' =>
                    'No se puede eliminar una nota de un periodo cerrado.',
            ]);
        }

        try {

            $inscripcionMateriaId = $nota->inscripcion_materia_id;

            DB::transaction(fn () => $nota->delete());

            return redirect()
                ->route('admin.academico.notas.index', $inscripcionMateriaId)
                ->with('exito', 'Nota eliminada correctamente.');

        } catch (RuntimeException $e) {

            return back()->withErrors(['error_academico' => $e->getMessage()]);

        } catch (Throwable $e) {

            return back()->withErrors(['error_academico' => 'No fue posible eliminar la nota.']);
        }
    }
}
