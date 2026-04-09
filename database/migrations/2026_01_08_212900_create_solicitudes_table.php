<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ==================================================
     * TABLA: solicitudes
     * ==================================================
     * Solicitudes de registro aún no aprobadas.
     */
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {

            /* ==========================
             | ID
             ========================== */
            $table->id();

            /* ==========================
             | Datos personales
             ========================== */
            $table->string('nombre', 255);
            $table->string('apellidos', 255);

            /* ==========================
             | Identificación (ÚNICA)
             ========================== */
            $table->string('identificacion', 30)->unique();

            /* ==========================
             | Contacto (NO único)
             ========================== */
            $table->string('correo', 255)->index();
            $table->string('ubicacion', 150);
            $table->string('contacto', 20);

            /* ==========================
             | Rol solicitado
             ========================== */
            $table->enum('rol', ['estudiante', 'docente'])->index();

            /* ==========================
             | Seguridad
             ========================== */
            $table->string('password');

            /* ==========================
             | Estado del proceso
             ========================== */
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])
                  ->default('pendiente')
                  ->index();

            /* ==========================
             | Timestamps
             ========================== */
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
