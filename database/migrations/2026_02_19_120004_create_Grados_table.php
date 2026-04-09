<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: grados
|--------------------------------------------------------------------------
| Representa el nivel académico dentro de una sede.
|
| Ejemplos:
| - Primero
| - Sexto
| - Undécimo
|
| Jerarquía:
| Sede → Grado → Grupo
|
| Reglas de negocio:
| - Un grado pertenece obligatoriamente a una sede.
| - No puede repetirse el mismo nivel dentro de una misma sede.
| - Si se elimina la sede, se eliminan automáticamente sus grados.
| - El tipo clasifica el nivel educativo (Preescolar, Primaria, Secundaria).
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grados', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria estándar
            |--------------------------------------------------------------------------
            */
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con Sede
            |--------------------------------------------------------------------------
            | Si se elimina la sede, se eliminan sus grados.
            */
            $table->foreignId('sede_id')
                  ->constrained('sedes')
                  ->cascadeOnDelete()
                  ->comment('Sede a la que pertenece el grado');

            /*
            |--------------------------------------------------------------------------
            | Información del grado
            |--------------------------------------------------------------------------
            */

            // Nombre descriptivo institucional
            $table->string('nombre', 100)
                  ->comment('Nombre visible del grado');

            // Nivel numérico académico (1–11 normalmente)
            $table->unsignedTinyInteger('nivel')
                  ->comment('Nivel numérico institucional del grado');

            /*
            |--------------------------------------------------------------------------
            | Clasificación académica
            |--------------------------------------------------------------------------
            | Se usa string en lugar de ENUM para mayor flexibilidad futura.
            | Ejemplos:
            | - Preescolar
            | - Primaria
            | - Secundaria
            */
            $table->string('tipo', 50)
                  ->default('Primaria')
                  ->comment('Clasificación académica del grado');

            /*
            |--------------------------------------------------------------------------
            | Estado del grado
            |--------------------------------------------------------------------------
            */
            $table->boolean('activo')
                  ->default(true)
                  ->index()
                  ->comment('Indica si el grado está habilitado');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Restricción única compuesta
            |--------------------------------------------------------------------------
            | No puede existir el mismo nivel dentro de la misma sede.
            */
            $table->unique(
                ['sede_id', 'nivel'],
                'grado_unico_por_sede'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grados');
    }
};