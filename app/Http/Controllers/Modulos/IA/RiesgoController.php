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
        // ── 1. Validar inputs ────────────────────────────────────────
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

        // ── 2. Validar porcentajes ───────────────────────────────────
        $suma = (float)$request->p1 + (float)$request->p2 + (float)$request->p3;
        if (round($suma, 2) !== 100.00) {
            return back()->withErrors([
                'porcentajes' => "Los porcentajes deben sumar 100%. Suma actual: {$suma}%"
            ])->withInput();
        }

        try {
            // ── 3. Guardar Excel temporal ────────────────────────────
            $excelPath = $request->file('excel')->store('ia_temp', 'local');
            $excelAbs  = storage_path('app/' . $excelPath);

            // ── 4. Directorio de salida ──────────────────────────────
            $outputDir = storage_path('app/ia_resultados');
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // ── 5. Buscar python3 en rutas conocidas de Nix ──────────
            $pythonBin  = '';
            $candidates = [
                '/nix/var/nix/profiles/default/bin/python3',
                '/root/.nix-profile/bin/python3',
                '/usr/bin/python3',
                '/usr/local/bin/python3',
                'python3',
            ];
            foreach ($candidates as $candidate) {
                if ($candidate === 'python3' || is_executable($candidate)) {
                    $pythonBin = $candidate;
                    break;
                }
            }

            $pythonScript = base_path('python/modelo_riesgo.py');
            $p1 = round((float)$request->p1 / 100, 4);
            $p2 = round((float)$request->p2 / 100, 4);
            $p3 = round((float)$request->p3 / 100, 4);

            // ── 6. Construir y ejecutar comando ──────────────────────
            $cmd = 'export PATH="/nix/var/nix/profiles/default/bin:/root/.nix-profile/bin:/usr/local/bin:/usr/bin:/bin:$PATH"'
                 . ' && python3'
                 . ' ' . escapeshellarg($pythonScript)
                 . ' ' . escapeshellarg($excelAbs)
                 . ' ' . (int)$request->periodo
                 . ' ' . $p1
                 . ' ' . $p2
                 . ' ' . $p3
                 . ' ' . escapeshellarg($outputDir)
                 . ' 2>&1';

            $jsonOutput = shell_exec($cmd);

            // ── 7. Limpiar Excel temporal ────────────────────────────
            Storage::disk('local')->delete($excelPath);

            // ── 8. Parsear respuesta ─────────────────────────────────
            $resultado = json_decode($jsonOutput, true);

            if (!$resultado || ($resultado['status'] ?? '') !== 'ok') {
                Log::error('IA Pipeline error', [
                    'script_existe' => file_exists($pythonScript) ? 'SI' : 'NO',
                    'python_bin'    => $pythonBin,
                    'output'        => substr($jsonOutput ?? 'null', 0, 600),
                ]);

                $detalle = $resultado['mensaje']
                    ?? substr($jsonOutput ?? 'Sin respuesta del script Python.', 0, 400);

                return back()
                    ->withErrors(['python' => 'Error en el modelo. Detalle: ' . $detalle])
                    ->withInput();
            }

            // ── 9. Retornar vista con resultados ─────────────────────
            return view('modulos.ia.riesgo.index', [
                'metricas'    => $resultado['metricas'],
                'estudiantes' => $resultado['estudiantes'],
                'periodo'     => $request->periodo,
                'p1'          => $request->p1,
                'p2'          => $request->p2,
                'p3'          => $request->p3,
            ]);

        } catch (\Throwable $e) {
            Log::error('IA Pipeline excepción PHP', [
                'mensaje' => $e->getMessage(),
                'linea'   => $e->getLine(),
                'archivo' => $e->getFile(),
            ]);

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