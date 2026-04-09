<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: materias
|--------------------------------------------------------------------------
| Representa las asignaturas académicas asociadas a un grado.
|
| Jerarquía:
| Sede → Grado → Materia
|
| Reglas de negocio:
| - Una materia pertenece obligatoriamente a un grado.
| - No puede existir el mismo nombre de materia dentro del mismo grado.
| - El código puede usarse como identificador institucional.
| - No debe eliminarse el grado si tiene materias asociadas.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria estándar
            |--------------------------------------------------------------------------
            */
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con Grado
            |--------------------------------------------------------------------------
            | Restrict evita eliminar un grado si tiene materias asociadas.
            */
            $table->foreignId('grado_id')
                  ->constrained('grados')
                  ->restrictOnDelete()
                  ->comment('Grado al que pertenece la materia');

            /*
            |--------------------------------------------------------------------------
            | Identificación institucional
            |--------------------------------------------------------------------------
            */
            $table->string('codigo', 20)
                  ->nullable()
                  ->comment('Código institucional de la materia');

            /*
            |--------------------------------------------------------------------------
            | Información académica
            |--------------------------------------------------------------------------
            */
            $table->string('nombre', 100)
                  ->comment('Nombre oficial de la materia');

            $table->unsignedTinyInteger('intensidad_horaria')
                  ->nullable()
                  ->comment('Número de horas semanales asignadas');

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            */
            $table->boolean('activa')
                  ->default(true)
                  ->index()
                  ->comment('Indica si la materia está habilitada');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Restricciones de unicidad
            |--------------------------------------------------------------------------
            */

            // No repetir nombre dentro del mismo grado
            $table->unique(
                ['grado_id', 'nombre'],
                'materia_unica_por_grado'
            );

            // Opcional: evitar repetir código dentro del mismo grado
            $table->unique(
                ['grado_id', 'codigo'],
                'codigo_unico_por_grado'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};