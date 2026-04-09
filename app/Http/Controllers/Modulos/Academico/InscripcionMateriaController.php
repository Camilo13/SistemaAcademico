<?php

namespace App\Http\Controllers\Modulos\Academico;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Inscripcion;
use App\Models\InscripcionMateria;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class InscripcionMateriaController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index — Materias de una inscripción
    |----------------------------------------------------------------------
    */
    public function index(Inscripcion $inscripcion)
    {
        $materias = $inscripcion->inscripcionMaterias()
            ->with([
                'asignacion.materia',
                'asignacion.docente',
                'notas',
            ])
            ->latest()
            ->get();

        return view(
            'modulos.academico.inscripcion_materia.index',
            compact('inscripcion', 'materias')
        );
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create(Inscripcion $inscripcion)
    {
        if (!$inscripcion->estaActiva()) {
            return back()->withErrors([
                'error_inscripcion_materia' =>
                    'No se pueden agregar materias a una inscripción inactiva.',
            ]);
        }

        $asignaciones = Asignacion::activa()
            ->where('grupo_id', $inscripcion->grupo_id)
            ->with(['materia', 'docente'])
            ->get();

        return view(
            'modulos.academico.inscripcion_materia.create',
            compact('inscripcion', 'asignaciones')
        );
    }

    /*
    |----------------------------------------------------------------------
    | store
    |----------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->reglas(),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($validated) {

                $inscripcion = Inscripcion::findOrFail($validated['inscripcion_id']);
                $asignacion  = Asignacion::findOrFail($validated['asignacion_id']);

                if (!$inscripcion->estaActiva()) {
                    throw new RuntimeException(
                        'No se pueden agregar materias a una inscripción inactiva.'
                    );
                }

                if ($asignacion->grupo_id !== $inscripcion->grupo_id) {
                    throw new RuntimeException(
                        'La asignación no pertenece al grupo del estudiante.'
                    );
                }

                InscripcionMateria::create([
                    'inscripcion_id' => $inscripcion->id,
                    'asignacion_id'  => $asignacion->id,
                    'grupo_id'       => $inscripcion->grupo_id,
                    'estado'         => 'activa',
                ]);
            });

            return redirect()
                ->route('admin.academico.inscripciones.materias.index', $validated['inscripcion_id'])
                ->with('exito', 'Materia inscrita correctamente.');

        } catch (QueryException $e) {

            return back()
                ->withErrors(['error_inscripcion_materia' => 'Esta materia ya fue registrada para el estudiante.'])
                ->withInput();

        } catch (RuntimeException $e) {

            return back()
                ->withErrors(['error_inscripcion_materia' => $e->getMessage()])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | retirar
    |----------------------------------------------------------------------
    */
    public function retirar(Inscripcion $inscripcion, InscripcionMateria $inscripcionMateria)
    {
        if (!$inscripcionMateria->estaActiva()) {
            return back()->withErrors([
                'error_inscripcion_materia' => 'La materia ya se encuentra retirada.',
            ]);
        }

        try {

            DB::transaction(fn () => $inscripcionMateria->retirar());

            return back()->with('exito', 'La materia fue retirada correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors([
                'error_inscripcion_materia' => 'No fue posible retirar la materia.',
            ]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(Inscripcion $inscripcion, InscripcionMateria $inscripcionMateria)
    {
        // Pre-check: no eliminar si tiene notas
        if ($inscripcionMateria->notas()->exists()) {
            return back()->withErrors([
                'error_inscripcion_materia' =>
                    'No se puede eliminar esta materia porque tiene ' .
                    $inscripcionMateria->notas()->count() . ' nota(s) registrada(s).',
            ]);
        }

        try {

            DB::transaction(fn () => $inscripcionMateria->delete());

            return redirect()
                ->route('admin.academico.inscripciones.materias.index', $inscripcion->id)
                ->with('exito', 'Materia eliminada de la inscripción correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors([
                'error_inscripcion_materia' => $e->getMessage(),
            ]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | Reglas y mensajes
    |----------------------------------------------------------------------
    */
    private function reglas(): array
    {
        return [
            'inscripcion_id' => ['required', 'exists:inscripciones,id'],
            'asignacion_id'  => ['required', 'exists:asignaciones,id'],
        ];
    }

    private function mensajes(): array
    {
        return [
            'inscripcion_id.required' => 'Debe indicar la inscripción.',
            'inscripcion_id.exists'   => 'La inscripción seleccionada no existe.',
            'asignacion_id.required'  => 'Debe seleccionar una materia.',
            'asignacion_id.exists'    => 'La asignación seleccionada no es válida.',
        ];
    }
}
