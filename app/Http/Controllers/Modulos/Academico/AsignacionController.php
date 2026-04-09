<?php

namespace App\Http\Controllers\Modulos\Academico;

use App\Http\Controllers\Controller;
use App\Models\AnioLectivo;
use App\Models\Asignacion;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class AsignacionController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Asignacion::with([
            'docente',
            'materia.grado',
            'grupo.grado.sede',
            'grupo.anioLectivo',
        ]);

        if ($request->filled('anio')) {
            $query->porAnio($request->anio);
        }

        if ($request->filled('docente')) {
            $query->where('docente_id', $request->docente);
        }

        if ($request->filled('grupo')) {
            $query->where('grupo_id', $request->grupo);
        }

        if ($request->filled('estado')) {
            $query->where('activa', $request->estado === 'activa');
        }

        $asignaciones = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // User::rol() — scope correcto del modelo
        $docentes = User::rol('docente')->orderBy('apellidos')->get();
        $grupos   = Grupo::with(['grado', 'anioLectivo'])->ordenados()->get();
        $anios    = AnioLectivo::orderByDesc('fecha_inicio')->get();

        return view(
            'modulos.academico.asignacion.index',
            compact('asignaciones', 'docentes', 'grupos', 'anios')
        );
    }

    /*
    |----------------------------------------------------------------------
    | create
    |----------------------------------------------------------------------
    */
    public function create()
    {
        // User::rol() — scope correcto (no role())
        $docentes = User::rol('docente')->orderBy('apellidos')->get();

        $materias = Materia::with('grado')
            ->activas()
            ->orderBy('nombre')
            ->get();

        $grupos = Grupo::with(['grado.sede', 'anioLectivo'])
            ->activo()
            ->ordenados()
            ->get();

        return view(
            'modulos.academico.asignacion.create',
            compact('docentes', 'materias', 'grupos')
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
                Asignacion::create([
                    'docente_id' => $validated['docente_id'],
                    'materia_id' => $validated['materia_id'],
                    'grupo_id'   => $validated['grupo_id'],
                    'activa'     => true,
                ]);
            });

            return redirect()
                ->route('admin.academico.asignaciones.index')
                ->with('exito', 'Asignación creada correctamente.');

        } catch (QueryException $e) {

            return back()
                ->withErrors(['error_asignacion' => 'Ya existe una asignación con ese docente, materia y grupo.'])
                ->withInput();

        } catch (RuntimeException $e) {

            return back()
                ->withErrors(['error_asignacion' => $e->getMessage()])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | edit
    |----------------------------------------------------------------------
    */
    public function edit(Asignacion $asignacion)
    {
        // User::rol() — scope correcto
        $docentes = User::rol('docente')->orderBy('apellidos')->get();

        $materias = Materia::with('grado')
            ->activas()
            ->orderBy('nombre')
            ->get();

        $grupos = Grupo::with(['grado.sede', 'anioLectivo'])
            ->activo()
            ->ordenados()
            ->get();

        return view(
            'modulos.academico.asignacion.edit',
            compact('asignacion', 'docentes', 'materias', 'grupos')
        );
    }

    /*
    |----------------------------------------------------------------------
    | update — Solo datos (docente, materia, grupo)
    | La visibilidad se maneja con activar/desactivar
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Asignacion $asignacion)
    {
        $validated = $request->validate(
            $this->reglasUpdate($asignacion),
            $this->mensajes()
        );

        try {

            DB::transaction(function () use ($asignacion, $validated) {
                $asignacion->update([
                    'docente_id' => $validated['docente_id'],
                    'materia_id' => $validated['materia_id'],
                    'grupo_id'   => $validated['grupo_id'],
                ]);
            });

            return redirect()
                ->route('admin.academico.asignaciones.index')
                ->with('exito', 'Asignación actualizada correctamente.');

        } catch (QueryException $e) {

            return back()
                ->withErrors(['error_asignacion' => 'Ya existe otra asignación con esa combinación.'])
                ->withInput();

        } catch (RuntimeException $e) {

            return back()
                ->withErrors(['error_asignacion' => $e->getMessage()])
                ->withInput();
        }
    }

    /*
    |----------------------------------------------------------------------
    | activar
    |----------------------------------------------------------------------
    */
    public function activar(Asignacion $asignacion)
    {
        try {

            DB::transaction(fn () => $asignacion->activar());

            return back()->with('exito', 'La asignación fue activada.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_asignacion' => 'No fue posible activar la asignación.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | desactivar
    |----------------------------------------------------------------------
    */
    public function desactivar(Asignacion $asignacion)
    {
        try {

            DB::transaction(fn () => $asignacion->desactivar());

            return back()->with('exito', 'La asignación fue desactivada.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_asignacion' => 'No fue posible desactivar la asignación.']);
        }
    }

    /*
    |----------------------------------------------------------------------
    | destroy
    |----------------------------------------------------------------------
    */
    public function destroy(Asignacion $asignacion)
    {
        // Pre-check: no eliminar si tiene notas
        if ($asignacion->notas()->exists()) {
            return back()->withErrors([
                'error_asignacion' =>
                    'No se puede eliminar esta asignación porque tiene ' .
                    $asignacion->notas()->count() . ' nota(s) registrada(s).',
            ]);
        }

        try {

            DB::transaction(fn () => $asignacion->delete());

            return redirect()
                ->route('admin.academico.asignaciones.index')
                ->with('exito', 'Asignación eliminada correctamente.');

        } catch (Throwable $e) {

            return back()->withErrors(['error_asignacion' => $e->getMessage()]);
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
            'docente_id' => ['required', 'exists:users,id'],
            'materia_id' => ['required', 'exists:materias,id'],
            'grupo_id'   => ['required', 'exists:grupos,id'],
        ];
    }

    private function reglasUpdate(Asignacion $asignacion): array
    {
        return [
            'docente_id' => ['required', 'exists:users,id'],
            'materia_id' => ['required', 'exists:materias,id'],
            'grupo_id'   => ['required', 'exists:grupos,id'],
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
            'docente_id.required' => 'Debe seleccionar un docente.',
            'docente_id.exists'   => 'El docente seleccionado no es válido.',
            'materia_id.required' => 'Debe seleccionar una materia.',
            'materia_id.exists'   => 'La materia seleccionada no es válida.',
            'grupo_id.required'   => 'Debe seleccionar un grupo.',
            'grupo_id.exists'     => 'El grupo seleccionado no es válido.',
        ];
    }
}
