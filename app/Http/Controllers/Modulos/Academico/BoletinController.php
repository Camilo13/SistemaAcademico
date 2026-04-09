<?php

namespace App\Http\Controllers\Modulos\Academico;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Services\BoletinService;

class BoletinController extends Controller
{
    protected BoletinService $boletinService;

    public function __construct(BoletinService $boletinService)
    {
        $this->boletinService = $boletinService;
    }

    /*
    |----------------------------------------------------------------------
    | show — Boletín anual del estudiante
    |----------------------------------------------------------------------
    */
    public function show(Inscripcion $inscripcion)
    {
        $boletin = $this->boletinService->generarBoletinAnual($inscripcion);

        return view('modulos.academico.boletin.show', [
            'inscripcion' => $inscripcion,
            'boletin'     => $boletin,
        ]);
    }

    /*
    |----------------------------------------------------------------------
    | exportarPdf — Previsualización / descarga PDF
    |----------------------------------------------------------------------
    | Para descarga real: composer require barryvdh/laravel-dompdf
    | y descomentar el bloque PDF::loadView() de abajo.
    |----------------------------------------------------------------------
    */
    public function exportarPdf(Inscripcion $inscripcion)
    {
        $boletin = $this->boletinService->generarBoletinAnual($inscripcion);

        /*
        |------------------------------------------------------------------
        | $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        |     'modulos.academico.boletin.pdf',
        |     compact('boletin', 'inscripcion')
        | );
        | return $pdf->download('boletin_' . $inscripcion->id . '.pdf');
        |------------------------------------------------------------------
        */

        return view('modulos.academico.boletin.pdf_preview', [
            'boletin'     => $boletin,
            'inscripcion' => $inscripcion,
        ]);
    }
}
