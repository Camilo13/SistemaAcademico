<?php

namespace App\Http\Controllers\Modulos\IA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RiesgoController extends Controller
{
    public function index()
    {
        return view('modulos.ia.riesgo.index');
    }

    public function analizar(Request $request)
    {
        $request->validate([
            'excel'   => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
            'periodo' => ['required', 'integer', 'in:1,2,3'],
            'p1'      => ['required', 'numeric', 'min:1', 'max:98'],
            'p2'      => ['required', 'numeric', 'min:1', 'max:98'],
            'p3'      => ['required', 'numeric', 'min:1', 'max:98'],
        ], [
            'excel.required'   => 'Debes subir un archivo Excel.',
            'excel.mimes'      => 'El archivo debe ser .xlsx o .xls.',
            'excel.max'        => 'El archivo no puede superar 5 MB.',
            'periodo.required' => 'Selecciona el periodo a analizar.',
            'periodo.in'       => 'El periodo debe ser 1, 2 o 3.',
            'p1.required'      => 'Ingresa el porcentaje de la Actividad 1.',
            'p2.required'      => 'Ingresa el porcentaje de la Actividad 2.',
            'p3.required'      => 'Ingresa el porcentaje de la Actividad 3.',
        ]);

        $suma = (float)$request->p1 + (float)$request->p2 + (float)$request->p3;
        if (round($suma, 2) !== 100.00) {
            return back()->withErrors([
                'porcentajes' => "Los porcentajes deben sumar 100%. Suma actual: {$suma}%"
            ])->withInput();
        }

        try {
            // ── DIAGNÓSTICO: encontrar ruta real de python3 ──────────
            $d1 = shell_exec('find /nix/store -name "python3" -type f 2>/dev/null | head -3');
            $d2 = shell_exec('find /nix -name "python3" 2>/dev/null | head -3');
            $d3 = shell_exec('which python3 2>&1');
            $d4 = shell_exec('ls /root/.nix-profile/bin/ 2>&1 | grep -i python');
            $d5 = base_path('python/modelo_riesgo.py');
            $d6 = file_exists($d5) ? 'SCRIPT SI EXISTE' : 'SCRIPT NO EXISTE';

            $info = "FIND NIX STORE: {$d1} | FIND NIX: {$d2} | WHICH: {$d3} | NIX PROFILE: {$d4} | SCRIPT: {$d6} en {$d5}";

            return back()->withErrors(['python' => $info])->withInput();
            // ── FIN DIAGNÓSTICO ──────────────────────────────────────

        } catch (\Throwable $e) {
            return back()
                ->withErrors(['python' => 'Error interno: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function descargarPdf(): BinaryFileResponse
    {
        $pdfPath = storage_path('app/ia_resultados/reporte_analitico.pdf');
        if (!file_exists($pdfPath)) {
            abort(404, 'El reporte aún no ha sido generado.');
        }
        return response()->download($pdfPath, 'reporte_riesgo_academico.pdf');
    }
}