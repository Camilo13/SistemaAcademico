<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModeloController extends Controller
{
    public function index()
    {
        return view('modelo.index');
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'materia' => 'required|string',
            'corte'   => 'required|integer|min:1|max:3',
            'p1'      => 'required|numeric|min:0|max:100',
            'p2'      => 'required|numeric|min:0|max:100',
            'p3'      => 'required|numeric|min:0|max:100',
            'excel'   => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        // Validar suma de porcentajes
        $total = $request->p1 + $request->p2 + $request->p3;
        if (abs($total - 100) > 0.01) {
            return back()->with('error', 'Los porcentajes deben sumar 100%');
        }

        // ================================
        // 1. CARPETA PRIVADA EXCELS
        // ================================
        $privatePath = storage_path('app/private/excels');
        
        if (!file_exists($privatePath)) {
            mkdir($privatePath, 0755, true);
        }

        // ================================
        // 2. GUARDAR ARCHIVO
        // ================================
        $excel = $request->file('excel');
        $originalName = pathinfo($excel->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $originalName) . '.' . $excel->getClientOriginalExtension();
        
        $excelPath = $privatePath . '/' . $filename;
        $excel->move($privatePath, $filename);

        if (!file_exists($excelPath)) {
            return back()->with('error', "Error al guardar el archivo Excel");
        }

        // ================================
        // 3. RUTAS PYTHON
        // ================================
        $pythonScript = base_path('python/app.py');
        
        if (!file_exists($pythonScript)) {
            return back()->with('error', "No se encuentra el script Python en: $pythonScript");
        }

        // Ruta del ejecutable Python (ajustar según tu instalación)
        $python = '"C:\Users\camil\AppData\Local\Programs\Python\Python313\python.exe"';
        
        // Opcional: probar con 'python' si está en PATH
        // $python = 'python';

        // ================================
        // 4. EJECUTAR PYTHON
        // ================================
        $command = $python . " " . escapeshellarg($pythonScript) . " "
            . escapeshellarg($request->materia) . " "
            . escapeshellarg($request->corte) . " "
            . escapeshellarg($request->p1) . " "
            . escapeshellarg($request->p2) . " "
            . escapeshellarg($request->p3) . " "
            . escapeshellarg($excelPath);

        Log::info("Ejecutando comando Python: " . $command);

        $output = [];
        $resultCode = 0;
        exec($command . " 2>&1", $output, $resultCode);

        Log::info("Código de salida: " . $resultCode);
        Log::info("Output: " . implode("\n", $output));

        if ($resultCode !== 0) {
            $errorMsg = implode("\n", $output);
            Log::error("Error en Python: " . $errorMsg);
            return back()->with('error', "Error al ejecutar el modelo: " . $errorMsg);
        }

        // ================================
        // 5. BUSCAR PDF GENERADO
        // ================================
        $pdfPath = base_path('python/storage/app/public/reporte_analisis/reporte_analitico.pdf');
        
        Log::info("Buscando PDF en: " . $pdfPath);

        if (!file_exists($pdfPath)) {
            // Intentar buscar archivos PDF en el directorio
            $pdfDir = base_path('python/storage/app/public/reporte_analisis/');
            if (file_exists($pdfDir)) {
                $pdfFiles = glob($pdfDir . "*.pdf");
                Log::info("PDFs encontrados en directorio: " . implode(", ", $pdfFiles));
                
                if (!empty($pdfFiles)) {
                    $pdfPath = $pdfFiles[0];
                } else {
                    return back()->with('error', 'No se generó el PDF correctamente. Verificar logs del servidor.');
                }
            } else {
                return back()->with('error', "No se encontró el directorio de reportes: $pdfDir");
            }
        }

        if (!file_exists($pdfPath)) {
            return back()->with('error', "No se pudo localizar el archivo PDF generado");
        }

        // ================================
        // 6. LIMPIAR ARCHIVO EXCEL TEMPORAL
        // ================================
        @unlink($excelPath);

        // ================================
        // 7. RETORNAR PDF PARA DESCARGA
        // ================================
        return response()->download($pdfPath, 'reporte_analitico_' . date('Ymd_His') . '.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="reporte_analitico.pdf"'
        ]);
    }
}