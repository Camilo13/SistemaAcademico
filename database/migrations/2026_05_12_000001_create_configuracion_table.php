<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: configuracion
|--------------------------------------------------------------------------
| Almacena parámetros globales del sistema (clave → valor).
|
| Ejemplos de uso:
| - nombre_institucion  → Nombre del colegio
| - nit                 → NIT de la institución
| - firma_rector        → Ruta de la imagen de firma del rector
| - encabezado_boletin  → Datos que aparecen en el encabezado del boletín
|
| Reglas de negocio:
| - Cada clave es única a nivel global.
| - El valor puede ser nulo cuando el parámetro aún no ha sido configurado.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria estándar
            |--------------------------------------------------------------------------
            */
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Identificador del parámetro
            |--------------------------------------------------------------------------
            */
            $table->string('clave', 100)
                  ->unique()
                  ->comment('Identificador único de la configuración');

            /*
            |--------------------------------------------------------------------------
            | Valor y descripción
            |--------------------------------------------------------------------------
            */
            $table->text('valor')
                  ->nullable()
                  ->comment('Valor de la configuración');

            $table->string('descripcion', 255)
                  ->nullable()
                  ->comment('Descripción legible del parámetro');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
};
