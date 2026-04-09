<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: asignaciones
|--------------------------------------------------------------------------
| Representa la asignación académica de un docente a:
| - Una materia
| - Un grupo
|
| Jerarquía estructural:
| Año Lectivo → Grupo → Asignación
|                    ↘
|                     Materia
|
| Reglas de negocio:
| - Un docente puede tener varias asignaciones.
| - No puede duplicarse la misma combinación docente + materia + grupo.
| - No debe eliminarse si existen notas asociadas.
| - El año lectivo se hereda desde el grupo (no se duplica aquí).
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria estándar
            |--------------------------------------------------------------------------
            */
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con Docente
            |--------------------------------------------------------------------------
            | Se asume que los docentes están en la tabla users.
            | Restrict evita eliminar un docente si tiene asignaciones activas.
            */
            $table->foreignId('docente_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('Docente asignado');

            /*
            |--------------------------------------------------------------------------
            | Relación con Materia
            |--------------------------------------------------------------------------
            */
            $table->foreignId('materia_id')
                  ->constrained('materias')
                  ->restrictOnDelete()
                  ->comment('Materia asignada');

            /*
            |--------------------------------------------------------------------------
            | Relación con Grupo
            |--------------------------------------------------------------------------
            | El año lectivo se obtiene desde el grupo.
            */
            $table->foreignId('grupo_id')
                  ->constrained('grupos')
                  ->restrictOnDelete()
                  ->comment('Grupo al que se imparte la materia');

            /*
            |--------------------------------------------------------------------------
            | Estado de la asignación
            |--------------------------------------------------------------------------
            */
            $table->boolean('activa')
                  ->default(true)
                  ->index()
                  ->comment('Indica si la asignación está vigente');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Restricción de unicidad
            |--------------------------------------------------------------------------
            | Evita duplicidad de docente + materia + grupo.
            */
            $table->unique(
                ['docente_id', 'materia_id', 'grupo_id'],
                'asignacion_unica'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};