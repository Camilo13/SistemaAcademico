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
    public function up(): void
    {
        Schema::create('bibliotecamateria', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------
            | CLAVE PRIMARIA
            |--------------------------------------------------------------
            */
            $table->id();

            /*
            |--------------------------------------------------------------
            | INFORMACIÓN PRINCIPAL
            |--------------------------------------------------------------
            */
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();

            /*
            |--------------------------------------------------------------
            | CONTROL ADMINISTRATIVO
            |--------------------------------------------------------------
            | Permite ocultar la materia sin eliminarla.
            */
            $table->boolean('visible')->default(true);

            /*
            |--------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------
            */
            $table->index('visible');
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bibliotecamateria');
    }
};