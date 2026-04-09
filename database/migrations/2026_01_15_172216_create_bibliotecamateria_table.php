<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Migración: bibliotecamateria
|--------------------------------------------------------------------------
| Representa las categorías o materias del módulo Biblioteca.

| No tiene relación con las materias académicas del sistema.
| Es exclusiva del módulo Biblioteca.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('bibliotecamateria', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | CLAVE PRIMARIA
            |--------------------------------------------------------------------------
            | Se usa id_materia para mantener coherencia con el modelo
            | BibliotecaMateria y con la relación en recursos.
            */
            $table->bigIncrements('id_materia');

            /*
            |--------------------------------------------------------------------------
            | INFORMACIÓN PRINCIPAL
            |--------------------------------------------------------------------------
            */
            $table->string('nombre', 150);          // Nombre visible
            $table->text('descripcion')->nullable(); // Descripción opcional

            /*
            |--------------------------------------------------------------------------
            | CONTROL ADMINISTRATIVO
            |--------------------------------------------------------------------------
            | Permite ocultar la materia sin eliminarla.
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
            | ÍNDICES
            |--------------------------------------------------------------------------
            */
            $table->index('visible');
            $table->index('nombre');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('bibliotecamateria');
    }
};