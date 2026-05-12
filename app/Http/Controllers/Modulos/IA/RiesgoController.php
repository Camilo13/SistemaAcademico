<?php

namespace App\Http\Controllers\Modulos\IA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RiesgoController extends Controller
{
    /**
     * Vista principal: formulario de carga.
     */
    public function index()
    {
        return view('modulos.ia.riesgo.index');
    }

    /**
     * Procesa el Excel llamando al script Python y retorna resultados.
     */
    public function analizar(Request $request)
    {
        // ----------------------------------------------------------------
        // 1. Validar inputs
        // ----------------------------------------------------------------
        $request->validate([
            'excel'  => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
            'corte'  => ['required', 'integer', 'in:1,2,3'],
            'p1'     => ['required', 'numeric', 'min:1', 'max:98'],
            'p2'     => ['required', 'numeric', 'min:1', 'max:98'],
            'p3'     => ['required', 'numeric', 'min:1', 'max:98'],
        ], [
            'excel.required'  => 'Debes subir un archivo Excel.',
            'excel.mimes'     => 'El archivo debe ser .xlsx o .xls.',
            'excel.max'       => 'El archivo no puede superar 5 MB.',
            'corte.required'  => 'Selecciona el corte a analizar.',
            'corte.in'        => 'El corte debe ser 1, 2 o 3.',
            'p1.required'     => 'Ingresa el porcentaje de la Actividad 1.',
            'p2.required'     => 'Ingresa el porcentaje de la Actividad 2.',
            'p3.required'     => 'Ingresa el porcentaje de la Actividad 3.',
        ]);

        // ----------------------------------------------------------------
        // 2. Validar que los porcentajes sumen 100
        // ----------------------------------------------------------------
        $suma = (float)$request->p1 + (float)$request->p2 + (float)$request->p3;
        if (round($suma, 2) !== 100.00) {
            return back()->withErrors([
                'porcentajes' => "Los porcentajes deben sumar 100%. Suma actual: {$suma}%"
            ])->withInput();
        }

        // ----------------------------------------------------------------
        // 3. Guardar Excel en storage temporal
        // ----------------------------------------------------------------
        $excelPath = $request->file('excel')->store('ia_temp', 'local');
        $excelAbs  = storage_path("app/{$excelPath}");

        // ----------------------------------------------------------------
        // 4. Directorio de salida para el PDF
        // ----------------------------------------------------------------
        $outputDir = storage_path('app/ia_resultados');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // ----------------------------------------------------------------
        // 5. Llamar al script Python
        // ----------------------------------------------------------------
        $pythonScript = base_path('python/modelo_riesgo.py');
        $p1 = (float)$request->p1 / 100;
        $p2 = (float)$request->p2 / 100;
        $p3 = (float)$request->p3 / 100;

        $cmd = sprintf(
            'python3 %s %s %d %.4f %.4f %.4f %s 2>&1',
            escapeshellarg($pythonScript),
            escapeshellarg($excelAbs),
            (int)$request->corte,
            $p1, $p2, $p3,
            escapeshellarg($outputDir)
        );

        $jsonOutput = shell_exec($cmd);

        // ----------------------------------------------------------------
        // 6. Limpiar Excel temporal
        // ----------------------------------------------------------------
        Storage::disk('local')->delete($excelPath);

        // ----------------------------------------------------------------
        // 7. Parsear respuesta del Python
        // ----------------------------------------------------------------
        $resultado = json_decode($jsonOutput, true);

        if (!$resultado || $resultado['status'] !== 'ok') {
            $msg = $resultado['mensaje'] ?? 'Error desconocido en el modelo Python.';
            return back()->withErrors(['python' => $msg])->withInput();
        }

        // ----------------------------------------------------------------
        // 8. Pasar datos a la vista
        // ----------------------------------------------------------------
        return view('modulos.ia.riesgo.index', [
            'metricas'    => $resultado['metricas'],
            'estudiantes' => $resultado['estudiantes'],
            'corte'       => $request->corte,
            'p1'          => $request->p1,
            'p2'          => $request->p2,
            'p3'          => $request->p3,
        ]);
    }

    /**
     * Descarga el PDF generado.
     */
    public function descargarPdf(): BinaryFileResponse
    {
        $pdfPath = storage_path('app/ia_resultados/reporte_analitico.pdf');

        if (!file_exists($pdfPath)) {
            abort(404, 'El reporte aún no ha sido generado.');
        }

        return response()->download($pdfPath, 'reporte_riesgo_academico.pdf');
    }
}