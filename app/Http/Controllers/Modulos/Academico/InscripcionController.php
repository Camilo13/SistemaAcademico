<?php

namespace App\Http\Controllers\Modulos\Academico;

use App\Http\Controllers\Controller;
use App\Models\AnioLectivo;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class InscripcionController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Inscripcion::with([
            'estudiante',
            'grupo.grado.sede',
            'grupo.anioLectivo',
        ]);

        if ($request->filled('anio')) {
            $query->porAnio($request->anio);
        }

        if ($request->filled('grupo')) {
            $query->where('grupo_id', $request->grupo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $inscripciones = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $grupos = Grupo::with(['grado', 'anioLectivo'])->activo()->ordenados()->get();
        $anios  = AnioLectivo::orderByDesc('fecha_inicio')->get();

        return view(
            'modulos.academico.inscripcion.index',
            compact('inscripciones', 'grupos', 'anios')
        );
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create()
    {
        // User::rol() — scope correcto del modelo
        $estudiantes = User::rol('estudiante')
            ->orderBy('apellidos')
            ->get();

        $grupos = Grupo::with(['grado.sede', 'anioLectivo'])
            ->activo()
            ->ordenados()
            ->get();

        return view(
            'modulos.academico.inscripcion.create',
            compact('estudiantes', 'grupos')
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
            $this->reglasStore(),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($validated) {

                $grupo = Grupo::findOrFail($validated['grupo_id']);

                if (!$grupo->estaActivo()) {
                    throw new RuntimeException('No se puede inscribir en un grupo inactivo.');
                }

                if (!$grupo->tieneCupo()) {
                    throw new RuntimeException('El grupo ya alcanzó el cupo máximo permitido.');
                }

                Inscripcion::create([
                    'estudiante_id'     => $validated['estudiante_id'],
                    'grupo_id'          => $validated['grupo_id'],
                    'fecha_inscripcion' => $validated['fecha_inscripcion'] ?? now(),
                    'estado'            => 'activa',
                ]);
            });

            return redirect()
                ->route('admin.academico.inscripciones.index')
                ->with('exito', 'Inscripción realizada correctamente.');

        } catch (QueryException $e) {

            return back()
                ->withErrors(['error_inscripcion' => 'El estudiante ya está inscrito en ese grupo.'])
                ->withInput();

        } catch (RuntimeException $e) {

            return back()
                ->withErrors(['error_inscripcion' => $e->getMessage()])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(Inscripcion $inscripcion)
    {
        $grupos = Grupo::with(['grado.sede', 'anioLectivo'])
            ->activo()
            ->ordenados()
            ->get();

        return view(
            'modulos.academico.inscripcion.edit',
            compact('inscripcion', 'grupos')
        );
    }

    /*
    |----------------------------------------------------------------------
    | update — Solo cambia el grupo (dentro del mismo año lectivo)
    | El estado se maneja con retirar/finalizar
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Inscripcion $inscripcion)
    {
        if (!$inscripcion->estaActiva()) {
            return back()->withErrors([
                'error_inscripcion' => 'Solo se pueden editar inscripciones activas.',
            ]);
        }

        $validated = $request->validate(
            $this->reglasUpdate(),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($inscripcion, $validated) {

                $grupoNuevo = Grupo::findOrFail($validated['grupo_id']);

                // No mover a otro año lectivo
                if ($grupoNuevo->anio_lectivo_id !== $inscripcion->grupo->anio_lectivo_id) {
                    throw new RuntimeException(
                        'No se puede mover la inscripción a otro año lectivo.'
                    );
                }

                // Verificar cupo si cambia de grupo
                if ($grupoNuevo->id !== $inscripcion->grupo_id && !$grupoNuevo->tieneCupo()) {
                    throw new RuntimeException(
                        'El grupo seleccionado no tiene cupo disponible.'
                    );
                }

                $inscripcion->update(['grupo_id' => $validated['grupo_id']]);
            });

            return redirect()
                ->route('admin.academico.inscripciones.index')
                ->with('exito', 'Inscripción actualizada correctamente.');

        } catch (RuntimeException $e) {

            return back()
                ->withErrors(['error_inscripcion' => $e->getMessage()])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | retirar
    |----------------------------------------------------------------------
    */
    public function retirar(Inscripcion $inscripcion)
    {
        if (!$inscripcion->estaActiva()) {
            return back()->withErrors([
                'error_inscripcion' => 'Solo se pueden retirar inscripciones activas.',
            ]);
        }

        try {

            DB::transaction(fn () => $inscripcion->retirar());

            return back()->with('exito', 'El estudiante fue retirado correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_inscripcion' => 'No fue posible retirar al estudiante.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | finalizar
    |----------------------------------------------------------------------
    */
    public function finalizar(Inscripcion $inscripcion)
    {
        if (!$inscripcion->estaActiva()) {
            return back()->withErrors([
                'error_inscripcion' => 'Solo se pueden finalizar inscripciones activas.',
            ]);
        }

        try {

            DB::transaction(fn () => $inscripcion->finalizar());

            return back()->with('exito', 'La inscripción fue finalizada correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_inscripcion' => 'No fue posible finalizar la inscripción.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(Inscripcion $inscripcion)
    {
        // Pre-check: no eliminar si tiene información académica
        if ($inscripcion->inscripcionMaterias()->exists() || $inscripcion->notas()->exists()) {
            return back()->withErrors([
                'error_inscripcion' =>
                    'No se puede eliminar esta inscripción porque tiene ' .
                    'materias o notas registradas.',
            ]);
        }

        try {

            DB::transaction(fn () => $inscripcion->delete());

            return redirect()
                ->route('admin.academico.inscripciones.index')
                ->with('exito', 'Inscripción eliminada correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_inscripcion' => $e->getMessage()]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | Reglas
    |----------------------------------------------------------------------
    */
    private function reglasStore(): array
    {
        return [
            'estudiante_id'     => ['required', 'exists:users,id'],
            'grupo_id'          => ['required', 'exists:grupos,id'],
            'fecha_inscripcion' => ['nullable', 'date'],
        ];
    }

    private function reglasUpdate(): array
    {
        return [
            'grupo_id' => ['required', 'exists:grupos,id'],
        ];
    }

    /*
    |----------------------------------------------------------------------
    | Mensajes
    |----------------------------------------------------------------------
    */
    private function mensajes(): array
    {
        return [
            'estudiante_id.required' => 'Debe seleccionar un estudiante.',
            'estudiante_id.exists'   => 'El estudiante seleccionado no es válido.',
            'grupo_id.required'      => 'Debe seleccionar un grupo.',
            'grupo_id.exists'        => 'El grupo seleccionado no es válido.',
            'estado.required'        => 'Debe seleccionar un estado.',
            'estado.in'              => 'El estado seleccionado no es válido.',
        ];
    }
}
