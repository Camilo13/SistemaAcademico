<?php

namespace App\Http\Controllers\Modulos\Docente;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\AnioLectivo;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| HorarioDocenteController
|--------------------------------------------------------------------------
| Muestra el horario semanal del docente autenticado.
| Solo lectura — agrupa sus horarios por día y bloque.
|--------------------------------------------------------------------------
*/

class HorarioDocenteController extends Controller
{
    public function index()
    {
        $docente = Auth::user();

        // Año lectivo activo
        $anio = AnioLectivo::where('activo', true)->first();

        // Horarios del docente en el año activo
        $horarios = Horario::with(['asignacion.materia', 'asignacion.grupo.grado'])
            ->whereHas('asignacion', fn($q) =>
                $q->where('docente_id', $docente->id)
                  ->where('activa', true)
                  ->whereHas('grupo', fn($qg) =>
                      $qg->where('anio_lectivo_id', optional($anio)->id)
                  )
            )
            ->get();

        // Construir cuadrícula día → bloque
        $cuadricula = [];
        foreach (Horario::DIAS as $dia) {
            foreach (array_keys(Horario::BLOQUES) as $bloque) {
                $cuadricula[$dia][$bloque] = null;
            }
        }
        foreach ($horarios as $horario) {
            $cuadricula[$horario->dia_semana][$horario->bloque] = $horario;
        }

        return view('modulos.docente.horario.index', compact(
            'cuadricula', 'anio', 'docente'
        ));
    }
}
