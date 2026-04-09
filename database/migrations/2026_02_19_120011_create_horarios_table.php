<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: horarios
|--------------------------------------------------------------------------
| Registra las franjas horarias semanales de cada asignación.
|
| Diseño:
|   - Se apoya en la tabla asignaciones (docente + materia + grupo ya
|     definidos allí). No se duplica ninguna de esas relaciones aquí.
|   - Los bloques son fijos para toda la institución (6 bloques/día):
|       Bloque 1 →  7:00 –  8:00   (Mañana)
|       Bloque 2 →  8:00 –  9:00   (Mañana)
|       [Refrigerio 9:00 – 9:30]
|       Bloque 3 →  9:30 – 10:30   (Media mañana)
|       Bloque 4 → 10:30 – 11:30   (Media mañana)
|       [Almuerzo 11:30 – 13:00]
|       Bloque 5 → 13:00 – 14:00   (Tarde)
|       Bloque 6 → 14:00 – 15:00   (Tarde)
|
| Restricciones:
|   - Un mismo grupo no puede tener dos materias al mismo tiempo
|     (unicidad a nivel de asignación + día + bloque).
|   - La validación de choque de docente se hace en el controlador.
|
| Cascada:
|   - Si se elimina una asignación, se eliminan sus horarios.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {

            $table->id();

            /*
            |------------------------------------------------------------------
            | Relación con Asignación
            |------------------------------------------------------------------
            | La asignación ya contiene: docente_id, materia_id, grupo_id.
            | Si se elimina la asignación, se eliminan sus horarios.
            */
            $table->foreignId('asignacion_id')
                  ->constrained('asignaciones')
                  ->cascadeOnDelete()
                  ->comment('Asignación académica (docente + materia + grupo)');

            /*
            |------------------------------------------------------------------
            | Día de la semana
            |------------------------------------------------------------------
            | Lunes a viernes — la institución no maneja clases el sábado.
            */
            $table->enum('dia_semana', [
                'lunes',
                'martes',
                'miercoles',
                'jueves',
                'viernes',
            ])->comment('Día de la semana');

            /*
            |------------------------------------------------------------------
            | Bloque horario fijo institucional (1 – 6)
            |------------------------------------------------------------------
            */
            $table->unsignedTinyInteger('bloque')
                  ->comment('Bloque horario institucional (1-6)');

            $table->timestamps();

            /*
            |------------------------------------------------------------------
            | Unicidad: el mismo par asignación + día + bloque no puede
            | repetirse (una materia no puede aparecer dos veces en el mismo
            | bloque del mismo día para el mismo grupo).
            |------------------------------------------------------------------
            */
            $table->unique(
                ['asignacion_id', 'dia_semana', 'bloque'],
                'horario_asignacion_dia_bloque_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
