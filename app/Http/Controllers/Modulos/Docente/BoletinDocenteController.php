<?php

namespace App\Http\Controllers\Modulos\Docente;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Services\BoletinService;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| BoletinDocenteController
|--------------------------------------------------------------------------
| Permite al docente ver el boletín de un estudiante que pertenezca
| a uno de sus grupos asignados. Solo lectura.
|
| Delega el cálculo en BoletinService (servicio ya existente).
|
| Ruta: GET /docente/boletin/{inscripcion}  →  docente.boletin.show
|--------------------------------------------------------------------------
*/

class BoletinDocenteController extends Controller
{
    public function __construct(protected BoletinService $boletinService) {}

    /*
    |----------------------------------------------------------------------
    | show
    |----------------------------------------------------------------------
    | Muestra el boletín de una inscripción.
    | Verifica que la inscripción pertenezca a un grupo del docente.
    |----------------------------------------------------------------------
    */
    public function show(Inscripcion $inscripcion)
    {
        // Cargar el grupo para verificar que el docente tiene asignación activa
        $inscripcion->load('grupo');

        // Seguridad: el docente debe tener al menos una asignación activa
        // en el mismo grupo de la inscripción
        $esDocente = $inscripcion->grupo
            ->asignaciones()
            ->where('docente_id', Auth::id())
            ->where('activa', true)
            ->exists();

        abort_unless($esDocente, 403, 'No tienes permiso para ver este boletín.');

        // Generar boletín usando el servicio compartido con admin
        $boletin = $this->boletinService->generarBoletinAnual($inscripcion);

        return view('modulos.docente.boletin.show', compact('inscripcion', 'boletin'));
    }
}
