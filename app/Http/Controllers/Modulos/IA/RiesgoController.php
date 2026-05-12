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

        $excelPath = $request->file('excel')->store('ia_temp', 'local');
        $excelAbs  = storage_path("app/{$excelPath}");

        $outputDir = storage_path('app/ia_resultados');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // En Railway (Nix), PHP no hereda el PATH del sistema.
        // Forzamos las rutas donde Nix instala Python.
        $nixPath = '/nix/var/nix/profiles/default/bin:'
                 . '/root/.nix-profile/bin:'
                 . '/usr/local/bin:/usr/bin:/bin';

        $pythonScript = base_path('python/modelo_riesgo.py');
        $p1 = (float)$request->p1 / 100;
        $p2 = (float)$request->p2 / 100;
        $p3 = (float)$request->p3 / 100;

        $cmd = sprintf(
            'export PATH="%s:$PATH" && python3 %s %s %d %.4f %.4f %.4f %s 2>&1',
            $nixPath,
            escapeshellarg($pythonScript),
            escapeshellarg($excelAbs),
            (int)$request->periodo,
            $p1, $p2, $p3,
            escapeshellarg($outputDir)
        );

        $jsonOutput = shell_exec($cmd);

        Storage::disk('local')->delete($excelPath);

        $resultado = json_decode($jsonOutput, true);

        if (!$resultado || $resultado['status'] !== 'ok') {
            Log::error('IA Pipeline - Error Python', [
                'python_bin'  => $pythonBin,
                'script'      => $pythonScript,
                'script_existe' => file_exists($pythonScript) ? 'SI' : 'NO',
                'cmd'         => $cmd,
                'output_raw'  => $jsonOutput,
            ]);

            $msg = $resultado['mensaje']
                ?? ('Error en el modelo. Detalle: ' . substr($jsonOutput ?? 'Sin salida del script', 0, 500));

            return back()->withErrors(['python' => $msg])->withInput();
        }

        return view('modulos.ia.riesgo.index', [
            'metricas'    => $resultado['metricas'],
            'estudiantes' => $resultado['estudiantes'],
            'periodo'     => $request->periodo,
            'p1'          => $request->p1,
            'p2'          => $request->p2,
            'p3'          => $request->p3,
        ]);
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