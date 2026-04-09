<?php

namespace App\Http\Controllers\Modulos\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Services\BoletinService;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| BoletinEstudianteController
|--------------------------------------------------------------------------
| Permite al estudiante ver sus boletines (año vigente + histórico)
| y descargar en PDF. Solo lectura.
|
| Delega el cálculo en BoletinService (servicio compartido con admin).
|
| Rutas:
|   GET /estudiante/boletines                    →  estudiante.boletin.index
|   GET /estudiante/boletines/{inscripcion}      →  estudiante.boletin.show
|   GET /estudiante/boletines/{inscripcion}/pdf  →  estudiante.boletin.pdf
|--------------------------------------------------------------------------
*/

class BoletinEstudianteController extends Controller
{
    public function __construct(protected BoletinService $boletinService) {}

    /*
    |----------------------------------------------------------------------
    | index
    |----------------------------------------------------------------------
    | Lista todas las inscripciones del estudiante agrupadas por año.
    | Sirve como selector para ver el boletín de cada año.
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $inscripciones = Inscripcion::with(['grupo.grado', 'grupo.anioLectivo'])
            ->where('estudiante_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('modulos.estudiante.boletin.index', compact('inscripciones'));
    }

    /*
    |----------------------------------------------------------------------
    | show
    |----------------------------------------------------------------------
    | Genera y muestra el boletín completo de una inscripción.
    | Verifica que la inscripción pertenezca al estudiante autenticado.
    |----------------------------------------------------------------------
    */
    public function show(Inscripcion $inscripcion)
    {
        // Seguridad: la inscripción debe pertenecer al estudiante autenticado
        abort_unless($inscripcion->estudiante_id === Auth::id(), 403);

        $boletin = $this->boletinService->generarBoletinAnual($inscripcion);

        return view('modulos.estudiante.boletin.show', compact('inscripcion', 'boletin'));
    }

    /*
    |----------------------------------------------------------------------
    | pdf
    |----------------------------------------------------------------------
    | Genera la vista de previsualización del PDF (o descarga real
    | si se instala barryvdh/laravel-dompdf).
    | Verifica que la inscripción pertenezca al estudiante autenticado.
    |----------------------------------------------------------------------
    */
    public function pdf(Inscripcion $inscripcion)
    {
        // Seguridad
        abort_unless($inscripcion->estudiante_id === Auth::id(), 403);

        $boletin = $this->boletinService->generarBoletinAnual($inscripcion);

        /*
        |--------------------------------------------------------------
        | Para descarga PDF real (descomentar al instalar dompdf):
        |   composer require barryvdh/laravel-dompdf
        |
        |   $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        |       'modulos.estudiante.boletin.pdf',
        |       compact('boletin', 'inscripcion')
        |   );
        |   return $pdf->download('boletin_' . $inscripcion->id . '.pdf');
        |--------------------------------------------------------------
        */

        return view('modulos.estudiante.boletin.pdf', compact('inscripcion', 'boletin'));
    }
}
