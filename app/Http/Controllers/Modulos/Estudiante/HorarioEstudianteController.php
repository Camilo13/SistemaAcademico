<?php

namespace App\Http\Controllers\Modulos\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\AnioLectivo;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| HorarioEstudianteController
|--------------------------------------------------------------------------
| Muestra el horario semanal del grupo en que está inscrito el estudiante.
| Solo lectura.
|--------------------------------------------------------------------------
*/

class HorarioEstudianteController extends Controller
{
    public function index()
    {
        $estudiante = Auth::user();

        // Inscripción activa del año lectivo activo
        $anio = AnioLectivo::where('activo', true)->first();

        $inscripcion = Inscripcion::with('grupo.grado')
            ->where('estudiante_id', $estudiante->id)
            ->where('estado', 'activa')
            ->whereHas('grupo', fn($q) =>
                $q->where('anio_lectivo_id', optional($anio)->id)
            )
            ->first();

        $cuadricula = [];
        foreach (Horario::DIAS as $dia) {
            foreach (array_keys(Horario::BLOQUES) as $bloque) {
                $cuadricula[$dia][$bloque] = null;
            }
        }

        if ($inscripcion) {
            $horarios = Horario::with(['asignacion.materia', 'asignacion.docente'])
                ->whereHas('asignacion', fn($q) =>
                    $q->where('grupo_id', $inscripcion->grupo_id)
                      ->where('activa', true)
                )
                ->get();

            foreach ($horarios as $horario) {
                $cuadricula[$horario->dia_semana][$horario->bloque] = $horario;
            }
        }

        return view('modulos.estudiante.horario.index', compact(
            'cuadricula', 'anio', 'inscripcion'
        ));
    }
}
