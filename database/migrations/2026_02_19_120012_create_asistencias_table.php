<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: asistencias
|--------------------------------------------------------------------------
| Registra el conteo de faltas de un estudiante por periodo y por materia.
|
| Modelo académico:
|   - La unidad mínima de registro es por PERIODO (no por día).
|   - El docente registra cuántas faltas acumuló el estudiante
|     en su asignación durante ese periodo.
|   - Una fila = un estudiante + una materia (asignación) + un periodo.
|
| Jerarquía:
|   Inscripción → InscripcionMateria → Asistencia → Periodo
|
| Reglas de negocio:
|   - Un solo registro por inscripcion_materia + periodo (UNIQUE).
|   - Las faltas deben ser >= 0.
|   - Solo se pueden modificar si el periodo está abierto.
|   - El docente solo puede registrar en sus propias asignaciones.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {

            /*
            |----------------------------------------------------------
            | Clave primaria
            |----------------------------------------------------------
            */
            $table->id();

            /*
            |----------------------------------------------------------
            | Relación con InscripcionMateria
            |----------------------------------------------------------
            | Identifica al estudiante + la materia específica.
            | Si se elimina la inscripcion_materia → cascada.
            */
            $table->foreignId('inscripcion_materia_id')
                  ->constrained('inscripcion_materias')
                  ->cascadeOnDelete()
                  ->comment('Materia inscrita del estudiante');

            /*
            |----------------------------------------------------------
            | Relación con Periodo
            |----------------------------------------------------------
            | El periodo define en qué corte se registró la asistencia.
            | No se permite eliminar el periodo si tiene asistencias.
            */
            $table->foreignId('periodo_id')
                  ->constrained('periodos')
                  ->restrictOnDelete()
                  ->comment('Periodo académico del registro');

            /*
            |----------------------------------------------------------
            | Conteo de faltas
            |----------------------------------------------------------
            | Número de clases no asistidas en el periodo.
            | justificadas: faltas con justificación válida.
            | injustificadas: faltas sin justificación.
            | El total = justificadas + injustificadas.
            */
            $table->unsignedSmallInteger('faltas_justificadas')
                  ->default(0)
                  ->comment('Faltas con justificación válida en el periodo');

            $table->unsignedSmallInteger('faltas_injustificadas')
                  ->default(0)
                  ->comment('Faltas sin justificación en el periodo');

            /*
            |----------------------------------------------------------
            | Observaciones opcionales
            |----------------------------------------------------------
            */
            $table->text('observacion')
                  ->nullable()
                  ->comment('Nota adicional sobre la asistencia del estudiante');

            /*
            |----------------------------------------------------------
            | Auditoría
            |----------------------------------------------------------
            */
            $table->timestamps();

            /*
            |----------------------------------------------------------
            | Restricción de unicidad
            |----------------------------------------------------------
            | Solo un registro de asistencia por materia + periodo.
            */
            $table->unique(
                ['inscripcion_materia_id', 'periodo_id'],
                'asistencia_unica_por_periodo'
            );

            /*
            |----------------------------------------------------------
            | Índices estratégicos
            |----------------------------------------------------------
            */
            $table->index(
                ['periodo_id', 'inscripcion_materia_id'],
                'idx_asistencia_periodo_materia'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
