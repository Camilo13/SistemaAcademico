<?php

namespace App\Services;

use App\Models\Configuracion;
use App\Models\Inscripcion;
use App\Models\InscripcionMateria;
use App\Models\Materia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class BoletinService
{
    protected CalculoAcademicoService $calculo;

    public function __construct(CalculoAcademicoService $calculo)
    {
        $this->calculo = $calculo;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERAR BOLETÍN
    |--------------------------------------------------------------------------
    */

    public function generarBoletin(Inscripcion $inscripcion): array
    {
        $inscripcion->load([
            'estudiante',
            'grupo.anioLectivo.periodos',
            'grupo.grado.director',
            'grupo.grado.sede',
            'inscripcionMaterias.asignacion.materia',
            'inscripcionMaterias.asignacion.docente',
            'inscripcionMaterias.notas',
        ]);

        $periodos        = $inscripcion->grupo->anioLectivo->periodos->sortBy('numero')->values();
        $materiasActivas = $inscripcion->inscripcionMaterias->where('estado', 'activa');

        // Separar normales y observación
        $materiasNormales    = $this->construirMaterias($materiasActivas->filter(
            fn ($m) => ($m->asignacion->materia->tipo ?? 'normal') === 'normal'
        ), $periodos);

        $materiasObservacion = $this->construirMaterias($materiasActivas->filter(
            fn ($m) => ($m->asignacion->materia->tipo ?? 'normal') === 'observacion'
        ), $periodos);

        // Promedio y puesto
        $promedioGeneral = $this->calculo->calcularPromedioAnual($inscripcion);
        $puesto          = $this->calcularPuesto($inscripcion, $promedioGeneral);

        // Configuración institucional
        $config = Configuracion::pluck('valor', 'clave');

        // Firma rector
        $firmaRector = $config['firma_rector'] ?? null;

        // Firma director de grado
        $director    = $inscripcion->grupo->grado->director ?? null;
        $firmaDirector = $director?->firma ?? null;

        return [
            // Datos institucionales
            'institucion' => [
                'nombre'      => $config['nombre_institucion'] ?? 'I.E.A. Akwe Uus Yat',
                'nit'         => $config['nit_institucion']    ?? '',
                'municipio'   => $config['municipio']          ?? '',
                'departamento'=> $config['departamento']       ?? '',
                'resolucion'  => $config['resolucion']         ?? '',
            ],

            // Datos del estudiante
            'estudiante' => [
                'id'     => $inscripcion->estudiante->id,
                'nombre' => $inscripcion->estudiante->nombre_completo ?? 'N/A',
            ],

            // Datos académicos
            'grado'       => $inscripcion->grupo->grado->nombre     ?? '—',
            'anio_lectivo'=> $inscripcion->grupo->anioLectivo->nombre ?? '—',
            'sede'        => $inscripcion->grupo->grado->sede->nombre ?? '—',

            // Periodos disponibles
            'periodos' => $periodos,

            // Materias
            'materias_normales'    => $materiasNormales,
            'materias_observacion' => $materiasObservacion,

            // Resumen
            'promedio_general'  => $promedioGeneral,
            'puesto'            => $puesto,
            'aprobado_anio'     => $this->calculo->estaAprobadoAnio($inscripcion),
            'total_materias'    => $materiasNormales->count(),
            'materias_aprobadas'=> $materiasNormales->where('aprobada', true)->count(),

            // Firmas
            'firma_rector'   => $firmaRector
                ? Storage::disk('public')->url($firmaRector)
                : null,
            'firma_director' => $firmaDirector
                ? Storage::disk('public')->url($firmaDirector)
                : null,
            'director_nombre'=> $director?->nombre_completo ?? '—',

            // Meta
            'fecha_generacion' => now()->format('d/m/Y'),
            'fecha_generacion_iso' => now()->format('Y-m-d H:i:s'),

            // Compatibilidad con vistas anteriores
            'grupo'              => $inscripcion->grupo->grado->nombre ?? '—',
            'materias'           => $materiasNormales,
            'materias_reprobadas'=> $materiasNormales->where('aprobada', false)->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CONSTRUIR DETALLE DE MATERIAS CON NOTAS POR PERIODO
    |--------------------------------------------------------------------------
    */

    protected function construirMaterias(
        Collection $materias,
        Collection $periodos
    ): Collection {

        return $materias->map(function (InscripcionMateria $im) use ($periodos) {

            $materia  = $im->asignacion->materia;
            $promedio = $this->calculo->calcularPromedioMateria($im);

            // Nota por periodo
            $notasPorPeriodo = [];
            foreach ($periodos as $periodo) {
                $nota = $im->notas->firstWhere('periodo_id', $periodo->id);
                $notasPorPeriodo[$periodo->numero] = $nota ? (float) $nota->nota : null;
            }

            return [
                'inscripcion_materia_id' => $im->id,
                'materia_id'             => $materia->id   ?? null,
                'materia_nombre'         => $materia->nombre ?? 'N/A',
                'descripcion'            => $materia->descripcion ?? null,
                'intensidad_horaria'     => $materia->intensidad_horaria ?? null,
                'notas_por_periodo'      => $notasPorPeriodo,
                'promedio'               => $promedio,
                'aprobada'               => !is_null($promedio) && $promedio >= 3.0,
                'desempeno'              => $this->resolverDesempeno($promedio),
                'desempeno_corto'        => $this->resolverDesempenoCorto($promedio),
                'estado_academico'       => $this->resolverDesempeno($promedio),
                'docente_nombre'         => $im->asignacion->docente->nombre_completo ?? 'N/A',
            ];
        })->values();
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULAR PUESTO DENTRO DEL GRUPO
    |--------------------------------------------------------------------------
    */

    protected function calcularPuesto(
        Inscripcion $inscripcion,
        ?float $promedioEstudiante
    ): ?int {

        if (is_null($promedioEstudiante)) {
            return null;
        }

        // Obtener todas las inscripciones activas del mismo grupo
        $inscripciones = Inscripcion::where('grupo_id', $inscripcion->grupo_id)
            ->where('estado', 'activa')
            ->with('inscripcionMaterias.notas')
            ->get();

        // Calcular promedio de cada estudiante
        $promedios = $inscripciones->map(function ($ins) {
            return [
                'id'       => $ins->id,
                'promedio' => $this->calculo->calcularPromedioAnual($ins) ?? 0,
            ];
        })->sortByDesc('promedio')->values();

        // Encontrar la posición del estudiante actual
        $posicion = $promedios->search(fn ($item) => $item['id'] === $inscripcion->id);

        return $posicion !== false ? $posicion + 1 : null;
    }

    /*
    |--------------------------------------------------------------------------
    | ESTADO DE DESEMPEÑO
    |--------------------------------------------------------------------------
    */

    protected function resolverDesempeno(?float $promedio): string
    {
        if (is_null($promedio)) return 'Sin calificar';
        if ($promedio >= 4.5)   return 'Superior';
        if ($promedio >= 4.0)   return 'Alto';
        if ($promedio >= 3.0)   return 'Básico';
        return 'Bajo';
    }

    protected function resolverDesempenoCorto(?float $promedio): string
    {
        if (is_null($promedio)) return '—';
        if ($promedio >= 4.5)   return 'S';
        if ($promedio >= 4.0)   return 'A';
        if ($promedio >= 3.0)   return 'B';
        return 'J';
    }

    /*
    |--------------------------------------------------------------------------
    | COMPATIBILIDAD — mantiene el método anterior por si algo lo usa
    |--------------------------------------------------------------------------
    */

    public function generarBoletinAnual(Inscripcion $inscripcion): array
    {
        return $this->generarBoletin($inscripcion);
    }
}
