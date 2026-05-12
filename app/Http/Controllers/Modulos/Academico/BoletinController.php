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
        $boletin = $this->boletinService->generarBoletin($inscripcion);

        return view('modulos.academico.boletin.show', [
            'inscripcion' => $inscripcion,
            'boletin'     => $boletin,
        ]);
    }

    public function exportarPdf(Inscripcion $inscripcion)
    {
        $boletin = $this->boletinService->generarBoletin($inscripcion);

        return view('modulos.academico.boletin.pdf_preview', [
            'boletin'     => $boletin,
            'inscripcion' => $inscripcion,
        ]);
    }
}
