<?php

namespace App\Http\Controllers\Modulos\Docente;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\AnioLectivo;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| MisGruposController
|--------------------------------------------------------------------------
| Muestra al docente autenticado sus asignaciones activas del año lectivo
| vigente. Una asignación = docente ↔ materia ↔ grupo.
|
| Ruta: GET /docente/grupos  →  docente.grupos.index
|--------------------------------------------------------------------------
*/

class MisGruposController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    | Carga las asignaciones activas del docente en el año activo.
    | Si no hay año activo, devuelve la colección vacía con aviso.
    |----------------------------------------------------------------------
    */
    public function index()
    {
        // Año lectivo activo (único por regla del modelo AnioLectivo)
        $anioActivo = AnioLectivo::where('activo', true)->first();

        // Asignaciones del docente autenticado filtradas por año activo
        $asignaciones = Asignacion::with([
                'materia',            // nombre de la materia
                'grupo.grado',        // nombre del grupo y su grado
                'grupo.anioLectivo',  // año lectivo del grupo
            ])
            ->where('docente_id', Auth::id())
            ->where('activa', true)
            ->when($anioActivo, fn ($q) =>
                $q->whereHas('grupo', fn ($g) =>
                    $g->where('anio_lectivo_id', $anioActivo->id)
                )
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return view('modulos.docente.grupos.index', compact('asignaciones', 'anioActivo'));
    }
}
