<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: anios_lectivos
|--------------------------------------------------------------------------
| Representa el ciclo académico institucional.
|
| Esta tabla es el contenedor principal del sistema evaluativo.
| De ella dependen:
| - Periodos académicos
| - Grupos
| - Inscripciones
| - Evaluaciones y notas
|
| Reglas de negocio:
| - Solo puede existir UN año activo a la vez (controlado en modelo).
| - No debe eliminarse si está activo.
| - Si se elimina un año inactivo, sus grupos se eliminan en cascada.
| - fecha_fin debe ser mayor que fecha_inicio (validado en aplicación).
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anios_lectivos', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria estándar
            |--------------------------------------------------------------------------
            */
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Nombre identificador del año
            |--------------------------------------------------------------------------
            | Ejemplos:
            | - 2025
            | - 2025-2026
            |
            | Debe ser único en el sistema.
            */
            $table->string('nombre', 20)
                  ->unique()
                  ->comment('Identificador oficial del año lectivo');

            /*
            |--------------------------------------------------------------------------
            | Rango oficial del calendario académico
            |--------------------------------------------------------------------------
            */
            $table->date('fecha_inicio')
                  ->comment('Fecha de inicio del año académico');

            $table->date('fecha_fin')
                  ->comment('Fecha de finalización del año académico');

            /*
            |--------------------------------------------------------------------------
            | Estado del año lectivo
            |--------------------------------------------------------------------------
            | Solo uno puede estar activo simultáneamente.
            | Se controla desde el modelo mediante lógica transaccional.
            */
            $table->boolean('activo')
                  ->default(false)
                  ->index()
                  ->comment('Indica si el año lectivo está actualmente en uso');

            /*
            |--------------------------------------------------------------------------
            | Auditoría básica
            |--------------------------------------------------------------------------
            */
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anios_lectivos');
    }
};