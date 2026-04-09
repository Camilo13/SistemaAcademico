<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();

            // Información principal del evento
            $table->string('titulo');
            $table->text('descripcion');

            // Ubicación física o virtual
            $table->string('lugar');

            // Fecha y hora del evento
            // Usamos dateTime porque necesitas fecha + hora exacta
            $table->dateTime('fecha_evento')->index();

            // Estado del evento
            // Solo eventos activos se muestran públicamente
            $table->boolean('activo')->default(true);

            // Timestamps del sistema
            $table->timestamps();

            // Índice adicional para consultas administrativas
            $table->index(['activo', 'fecha_evento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
