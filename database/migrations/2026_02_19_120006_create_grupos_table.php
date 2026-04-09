<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: grupos
|--------------------------------------------------------------------------
| Representa la unidad académica operativa dentro de un año lectivo.
|
| Ejemplo:
| - Sexto A - 2025
|
| Jerarquía:
| Sede → Grado → Grupo → Estudiante
|
| Reglas de negocio:
| - Un grupo pertenece obligatoriamente a un grado.
| - Un grupo pertenece obligatoriamente a un año lectivo.
| - No puede repetirse el mismo identificador de grupo dentro del mismo
|   grado y año lectivo.
| - Si se elimina el año lectivo, sus grupos se eliminan en cascada.
| - No puede eliminarse un grado si tiene grupos asociados.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {

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
            | Restrict evita eliminar un grado si tiene grupos asociados.
            | La sede se hereda indirectamente desde el grado.
            */
            $table->foreignId('grado_id')
                  ->constrained('grados')
                  ->restrictOnDelete()
                  ->comment('Grado al que pertenece el grupo');

            /*
            |--------------------------------------------------------------------------
            | Relación con Año Lectivo
            |--------------------------------------------------------------------------
            | Si se elimina el año, se eliminan automáticamente sus grupos.
            */
            $table->foreignId('anio_lectivo_id')
                  ->constrained('anios_lectivos')
                  ->cascadeOnDelete()
                  ->comment('Año lectivo al que pertenece el grupo');

            /*
            |--------------------------------------------------------------------------
            | Identificación del grupo
            |--------------------------------------------------------------------------
            | Ejemplo:
            | A, B, C
            */
            $table->string('nombre', 10)
                  ->comment('Identificador interno del grupo');

            /*
            |--------------------------------------------------------------------------
            | Cupo máximo permitido
            |--------------------------------------------------------------------------
            */
            $table->unsignedSmallInteger('cupo_maximo')
                  ->nullable()
                  ->comment('Cantidad máxima de estudiantes permitidos');

            /*
            |--------------------------------------------------------------------------
            | Estado operativo
            |--------------------------------------------------------------------------
            */
            $table->boolean('activo')
                  ->default(true)
                  ->index()
                  ->comment('Indica si el grupo está habilitado');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Restricción única estructural
            |--------------------------------------------------------------------------
            | No puede existir el mismo grupo (A, B, etc.)
            | dentro del mismo grado y año lectivo.
            */
            $table->unique(
                ['grado_id', 'anio_lectivo_id', 'nombre'],
                'grupo_unico_por_anio'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};