<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: notas
|--------------------------------------------------------------------------
| Representa la calificación obtenida por un estudiante
| en una materia específica durante un periodo académico.
|
| Jerarquía:
| Inscripción → InscripcionMateria → Nota → Periodo
|
| Reglas:
| - Una sola nota por periodo por materia.
| - Rango válido: 0.00 a 5.00
| - Redondeo se realiza en capa de cálculo.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con InscripciónMateria
            |--------------------------------------------------------------------------
            */
            $table->foreignId('inscripcion_materia_id')
                  ->constrained('inscripcion_materias')
                  ->cascadeOnDelete()
                  ->comment('Materia inscrita por el estudiante');

            /*
            |--------------------------------------------------------------------------
            | Relación con Periodo
            |--------------------------------------------------------------------------
            */
            $table->foreignId('periodo_id')
                  ->constrained('periodos')
                  ->restrictOnDelete()
                  ->comment('Periodo académico evaluado');

            /*
            |--------------------------------------------------------------------------
            | Nota numérica
            |--------------------------------------------------------------------------
            */
            $table->decimal('nota', 4, 2)
                  ->comment('Calificación numérica 0.00 - 5.00');

            $table->text('observacion')
                  ->nullable()
                  ->comment('Observaciones académicas opcionales');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Restricción única estructural
            |--------------------------------------------------------------------------
            */
            $table->unique(
                ['inscripcion_materia_id', 'periodo_id'],
                'nota_unica_por_periodo'
            );

            /*
            |--------------------------------------------------------------------------
            | Índice estratégico
            |--------------------------------------------------------------------------
            */
            $table->index(
                ['periodo_id', 'inscripcion_materia_id'],
                'idx_periodo_materia'
            );

            /*
            |--------------------------------------------------------------------------
            | Validación estructural (MySQL 8+)
            |--------------------------------------------------------------------------
            */
            // $table->check('nota >= 0 AND nota <= 5');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};