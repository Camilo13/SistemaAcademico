<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: inscripcion_materias
|--------------------------------------------------------------------------
| Representa las materias que un estudiante cursa dentro de su inscripción.
|
| Jerarquía:
| Grupo
|   ├── Inscripción
|   └── Asignación
|           └── Materia
|
| Reglas:
| - Solo puede registrar asignaciones del mismo grupo.
| - No puede duplicarse la misma asignación.
| - Base directa del sistema de notas.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion_materias', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria
            |--------------------------------------------------------------------------
            */
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con Inscripción
            |--------------------------------------------------------------------------
            */
            $table->foreignId('inscripcion_id')
                  ->constrained('inscripciones')
                  ->cascadeOnDelete()
                  ->comment('Inscripción académica del estudiante');

            /*
            |--------------------------------------------------------------------------
            | Relación con Asignación
            |--------------------------------------------------------------------------
            */
            $table->foreignId('asignacion_id')
                  ->constrained('asignaciones')
                  ->restrictOnDelete()
                  ->comment('Asignación docente-materia-grupo');

            /*
            |--------------------------------------------------------------------------
            | Refuerzo estructural: Grupo
            |--------------------------------------------------------------------------
            | Garantiza coherencia entre inscripción y asignación.
            */
            $table->foreignId('grupo_id')
                  ->constrained('grupos')
                  ->restrictOnDelete()
                  ->comment('Grupo académico relacionado');

            /*
            |--------------------------------------------------------------------------
            | Estado académico de la materia
            |--------------------------------------------------------------------------
            */
            $table->string('estado', 20)
                  ->default('activa')
                  ->index()
                  ->comment('Estado de la materia para el estudiante');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Restricción única
            |--------------------------------------------------------------------------
            */
            $table->unique(
                ['inscripcion_id', 'asignacion_id'],
                'inscripcion_materia_unica'
            );

            /*
            |--------------------------------------------------------------------------
            | Índices estratégicos
            |--------------------------------------------------------------------------
            */
            $table->index(
                ['grupo_id', 'asignacion_id'],
                'idx_grupo_asignacion'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion_materias');
    }
};