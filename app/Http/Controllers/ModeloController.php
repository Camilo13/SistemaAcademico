<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function index()
    {
        return view('modelo.index');
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'materia' => 'required',
            'corte' => 'required',
            'p1' => 'required',
            'p2' => 'required',
            'p3' => 'required',
            'excel' => 'required|mimes:xlsx,xls'
        ]);

        $excel = $request->file('excel');

        $excelPath = storage_path(
            'app/' . $excel->store('excels')
        );

        $pythonScript = base_path('python/app.py');

        $command = "python3 \"$pythonScript\" "
            . escapeshellarg($request->materia) . " "
            . escapeshellarg($request->corte) . " "
            . escapeshellarg($request->p1) . " "
            . escapeshellarg($request->p2) . " "
            . escapeshellarg($request->p3) . " "
            . escapeshellarg($excelPath);

        exec($command . " 2>&1", $output, $resultCode);

        dd($output);

        if ($resultCode !== 0) {
            return back()->with(
                'error',
                implode("\n", $output)
            );
        }

        $pdfPath = base_path(
            'python/resultados_modelo/reporte_analitico.pdf'
        );

        return response()->download($pdfPath);
    }
}