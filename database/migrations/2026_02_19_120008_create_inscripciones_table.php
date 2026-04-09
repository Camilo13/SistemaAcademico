<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: inscripciones
|--------------------------------------------------------------------------
| Representa la matrícula oficial de un estudiante en un grupo.
|
| Jerarquía:
| Año Lectivo → Grupo → Inscripción → Estudiante
|
| Reglas:
| - Un estudiante solo puede inscribirse una vez en un grupo.
| - El año lectivo se hereda desde el grupo.
| - La validación de "una inscripción por año" se controla en aplicación.
| - No debe eliminarse si existen dependencias académicas.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria
            |--------------------------------------------------------------------------
            */
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con Estudiante
            |--------------------------------------------------------------------------
            */
            $table->foreignId('estudiante_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('Usuario con rol estudiante');

            /*
            |--------------------------------------------------------------------------
            | Relación con Grupo
            |--------------------------------------------------------------------------
            */
            $table->foreignId('grupo_id')
                  ->constrained('grupos')
                  ->restrictOnDelete()
                  ->comment('Grupo académico asignado');

            /*
            |--------------------------------------------------------------------------
            | Estado académico
            |--------------------------------------------------------------------------
            */
            $table->string('estado', 20)
                  ->default('activa')
                  ->index()
                  ->comment('Estado de la inscripción');

            /*
            |--------------------------------------------------------------------------
            | Fecha formal de matrícula
            |--------------------------------------------------------------------------
            */
            $table->date('fecha_inscripcion')
                  ->nullable()
                  ->comment('Fecha oficial de inscripción');

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
                ['estudiante_id', 'grupo_id'],
                'inscripcion_unica_por_grupo'
            );

            /*
            |--------------------------------------------------------------------------
            | Índice estratégico
            |--------------------------------------------------------------------------
            */
            $table->index(
                ['grupo_id', 'estado'],
                'idx_grupo_estado'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};