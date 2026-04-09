<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ==================================================
     * CREACIÓN DE LA TABLA USERS
     * ==================================================
     * Tabla principal de usuarios autenticados del sistema
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            /* =====================================================
             | Identificador interno
             ===================================================== */
            $table->id();

            /* =====================================================
             | Información personal
             ===================================================== */
            $table->string('nombre', 255);
            $table->string('apellidos', 255);

            /* =====================================================
             | Identificación real (ÚNICA)
             ===================================================== */
            $table->string('identificacion', 30)->unique();

            /* =====================================================
             | Información de contacto (NO única)
             ===================================================== */
            $table->string('correo', 255)->index();
            $table->string('ubicacion', 150)->nullable();
            $table->string('contacto', 20)->nullable();

            /* =====================================================
             | Credenciales y control de acceso
             ===================================================== */
            $table->string('password');

            $table->enum('rol', ['administrador', 'docente', 'estudiante'])
                  ->index();

            $table->boolean('activo')
                  ->default(true)
                  ->index();

            /* =====================================================
             | Tokens y marcas de tiempo
             ===================================================== */
            $table->rememberToken();
            $table->timestamps();
        });

        /* =====================================================
         | SESIONES (SESSION_DRIVER=database)
         ===================================================== */
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * ==================================================
     * REVERSIÓN
     * ==================================================
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
