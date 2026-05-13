<?php

namespace App\Http\Controllers\Modulos\IA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
            // ── DIAGNÓSTICO FINAL ────────────────────────────────────
            $p1 = new Process(['which', 'python3']);
            $p1->run();
            $which = trim($p1->getOutput());

            $p2 = new Process(['find', '/nix', '-name', 'python3', '-type', 'f']);
            $p2->setTimeout(10);
            $p2->run();
            $find = trim($p2->getOutput());

            $p3 = new Process(['env']);
            $p3->run();
            $env = substr($p3->getOutput(), 0, 400);

            return back()->withErrors([
                'python' => "WHICH:[{$which}] | FIND:[{$find}] | ENV:[{$env}]"
            ])->withInput();
            // ── FIN DIAGNÓSTICO ──────────────────────────────────────

        } catch (\Throwable $e) {
            return back()
                ->withErrors(['python' => 'Error interno: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function descargarPdf()
    {
        $pdfPath = storage_path('app/ia_resultados/reporte_analitico.pdf');
        if (!file_exists($pdfPath)) {
            abort(404, 'El reporte aún no ha sido generado.');
        }
        return response()->download($pdfPath, 'reporte_riesgo_academico.pdf');
    }

    private function detectarPython(): string
    {
        $candidatos = [
            'python',
            'python3',
            'C:\\Python311\\python.exe',
            'C:\\Python310\\python.exe',
            '/usr/bin/python3',
            '/usr/local/bin/python3',
            '/nix/var/nix/profiles/default/bin/python3',
        ];

        foreach ($candidatos as $bin) {
            if (str_contains($bin, '\\') || str_contains($bin, '/')) {
                if (file_exists($bin)) return $bin;
            } else {
                $check = new Process([$bin, '--version']);
                try {
                    $check->run();
                    if ($check->isSuccessful()) return $bin;
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }
        return 'python3';
    }

    private function getEnv(): array
    {
        $pathExtra = implode(PATH_SEPARATOR, [
            '/nix/var/nix/profiles/default/bin',
            '/root/.nix-profile/bin',
            '/usr/local/bin',
            '/usr/bin',
            '/bin',
        ]);

        return array_merge($_SERVER, [
            'PATH' => $pathExtra . PATH_SEPARATOR . (getenv('PATH') ?: ''),
        ]);
    }
}