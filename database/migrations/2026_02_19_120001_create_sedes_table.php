<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: sedes
|--------------------------------------------------------------------------
| Representa las sedes físicas o institucionales del colegio.
|
| Relación jerárquica:
| Sede → Grado → Grupo
|
| Reglas:
| - No debe eliminarse si tiene grados o grupos asociados.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sedes', function (Blueprint $table) {

            // clave primaria estándar
            $table->id();

            // Código institucional opcional
            $table->string('codigo', 20)
                  ->unique()
                  ->nullable();

            // Nombre oficial
            $table->string('nombre', 100);

            // Información de contacto
            $table->string('direccion', 150)->nullable();
            $table->string('telefono', 20)->nullable();

            // Control administrativo
            $table->boolean('activa')
                  ->default(true)
                  ->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};