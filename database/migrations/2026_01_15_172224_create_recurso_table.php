<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Migración: recurso
|--------------------------------------------------------------------------
| Representa los recursos educativos del módulo Biblioteca.
|
| Cada recurso pertenece a una BibliotecaMateria.
| Si se elimina la materia, se eliminan sus recursos.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('recurso', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | CLAVE PRIMARIA
            |--------------------------------------------------------------------------
            */
            $table->bigIncrements('id_recurso');

            /*
            |--------------------------------------------------------------------------
            | RELACIÓN CON BIBLIOTECAMATERIA
            |--------------------------------------------------------------------------
            | Cada recurso pertenece a una materia del módulo biblioteca.
            */
            $table->unsignedBigInteger('id_materia');

            /*
            |--------------------------------------------------------------------------
            | INFORMACIÓN BÁSICA
            |--------------------------------------------------------------------------
            */
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();

            /*
            |--------------------------------------------------------------------------
            | TIPO DE RECURSO
            |--------------------------------------------------------------------------
            | archivo | video | audio | enlace | imagen
            */
            $table->string('tipo', 20);

            /*
            |--------------------------------------------------------------------------
            | ORIGEN DEL RECURSO
            |--------------------------------------------------------------------------
            | archivo | url
            */
            $table->string('origen', 20);

            /*
            |--------------------------------------------------------------------------
            | UBICACIÓN
            |--------------------------------------------------------------------------
            | - Ruta en storage (si es archivo)
            | - Enlace externo (si es url)
            */
            $table->string('url', 500);

            /*
            |--------------------------------------------------------------------------
            | MIME TYPE
            |--------------------------------------------------------------------------
            | Solo aplica cuando origen = archivo
            */
            $table->string('mime_type', 100)->nullable();

            /*
            |--------------------------------------------------------------------------
            | AUTOR / FUENTE
            |--------------------------------------------------------------------------
            */
            $table->string('autor', 150)->nullable();

            /*
            |--------------------------------------------------------------------------
            | CONTROL DE VISIBILIDAD
            |--------------------------------------------------------------------------
            */
            $table->boolean('visible')->default(true);

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | FOREIGN KEY
            |--------------------------------------------------------------------------
            | Si se elimina la materia → se eliminan sus recursos.
            */
            $table->foreign('id_materia')
                ->references('id_materia')
                ->on('bibliotecamateria')
                ->onDelete('cascade');

            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */
            $table->index('id_materia');
            $table->index('tipo');
            $table->index('origen');
            $table->index('visible');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurso');
    }
};