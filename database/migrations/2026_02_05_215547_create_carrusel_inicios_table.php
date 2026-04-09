<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ==========================================================
     * TABLA: carrusel_inicios
     * ----------------------------------------------------------
     * Administra las imágenes del carrusel principal del hero.
     *
     * Reglas estructurales:
     * - Orden único (no se permiten duplicados)
     * - Imagen obligatoria
     * - Estado activo indexado
     * ==========================================================
     */
    public function up(): void
    {
        Schema::create('carrusel_inicios', function (Blueprint $table) {

            // Identificador primario
            $table->id();

            // Ruta relativa en storage (disk public)
            $table->string('imagen', 255);

            /**
             * Orden de aparición en el carrusel.
             * Se define como UNIQUE para garantizar
             * integridad de presentación.
             */
            $table->unsignedInteger('orden')
                  ->default(0)
                  ->unique();

            /**
             * Control de visibilidad.
             * Indexado para consultas rápidas
             * en el scope activosOrdenados().
             */
            $table->boolean('activo')
                  ->default(true)
                  ->index();

            // Control de auditoría básica
            $table->timestamps();
        });
    }

    /**
     * Reversión segura
     */
    public function down(): void
    {
        Schema::dropIfExists('carrusel_inicios');
    }
};
