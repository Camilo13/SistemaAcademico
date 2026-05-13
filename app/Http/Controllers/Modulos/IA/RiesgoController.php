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

            // ── 5. Preparar argumentos JSON para Python ──────────────
            $args = json_encode([
                'excel_path' => $excelAbs,
                'periodo'    => (int)$request->periodo,
                'p1'         => round((float)$request->p1 / 100, 4),
                'p2'         => round((float)$request->p2 / 100, 4),
                'p3'         => round((float)$request->p3 / 100, 4),
                'output_dir' => $outputDir,
            ]);

            // ── 6. Detectar python3 compatible Windows/Linux ─────────
            $pythonScript = base_path('python/modelo_riesgo.py');
            $pythonBin    = $this->detectarPython();

            // ── 7. Ejecutar con Symfony Process ─────────────────────
            $process = new Process(
                [$pythonBin, $pythonScript, $args],
                base_path(),        // working directory
                $this->getEnv(),    // environment con PATH correcto
                null,
                120                 // timeout 2 minutos
            );

            $process->run();

            // ── 8. Limpiar Excel temporal ────────────────────────────
            Storage::disk('local')->delete($excelPath);

            // ── 9. Verificar ejecución ───────────────────────────────
            if (!$process->isSuccessful()) {
                Log::error('IA Pipeline - Process failed', [
                    'exit_code' => $process->getExitCode(),
                    'stderr'    => $process->getErrorOutput(),
                    'stdout'    => $process->getOutput(),
                ]);

                $detalle = $process->getErrorOutput() ?: $process->getOutput();
                return back()
                    ->withErrors(['python' => 'Error en Python: ' . substr($detalle, 0, 400)])
                    ->withInput();
            }

            // ── 10. Parsear JSON ─────────────────────────────────────
            $resultado = json_decode($process->getOutput(), true);

            if (!$resultado || ($resultado['status'] ?? '') !== 'ok') {
                $msg = $resultado['mensaje'] ?? $process->getOutput();
                return back()
                    ->withErrors(['python' => 'Error en el modelo: ' . substr($msg, 0, 400)])
                    ->withInput();
            }

            // ── 11. Retornar vista ───────────────────────────────────
            return view('modulos.ia.riesgo.index', [
                'metricas'    => $resultado['metricas'],
                'estudiantes' => $resultado['estudiantes'],
                'periodo'     => $request->periodo,
                'p1'          => $request->p1,
                'p2'          => $request->p2,
                'p3'          => $request->p3,
            ]);

        } catch (\Throwable $e) {
            Log::error('IA Pipeline excepción', [
                'mensaje' => $e->getMessage(),
                'linea'   => $e->getLine(),
            ]);
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

    // ── Detectar binario Python (Windows y Linux) ────────────────────
    private function detectarPython(): string
    {
        // Windows XAMPP / PATH local
        $candidatos = [
            'python',                                          // Windows PATH
            'python3',                                         // Linux/Mac PATH
            'C:\\Python311\\python.exe',                       // Windows instalación típica
            'C:\\Python310\\python.exe',
            'C:\\Users\\' . get_current_user() . '\\AppData\\Local\\Programs\\Python\\Python311\\python.exe',
            '/usr/bin/python3',                                // Linux
            '/usr/local/bin/python3',
            '/nix/var/nix/profiles/default/bin/python3',       // Railway Nix
        ];

        foreach ($candidatos as $bin) {
            if (str_contains($bin, '\\') || str_contains($bin, '/')) {
                if (file_exists($bin)) return $bin;
            } else {
                // Verificar en PATH con Process
                $check = new Process([$bin, '--version']);
                try {
                    $check->run();
                    if ($check->isSuccessful()) return $bin;
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        return 'python3'; // fallback
    }

    // ── Environment con PATH extendido para Railway ──────────────────
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