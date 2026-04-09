<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Tabla: periodos
|--------------------------------------------------------------------------
| Representa las divisiones evaluativas de un año lectivo.
|
| Jerarquía:
| Año Lectivo → Periodos → Evaluaciones / Notas
|
| Reglas académicas:
| - Cada periodo pertenece obligatoriamente a un año lectivo.
| - Un año no puede tener dos periodos con el mismo número.
| - El número normalmente va de 1 a 3 (validado en aplicación).
| - Las fechas deben estar dentro del rango del año lectivo.
| - El estado controla si se pueden registrar o modificar notas.
| - Si se elimina el año lectivo, sus periodos se eliminan en cascada.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria
            |--------------------------------------------------------------------------
            */
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con Año Lectivo
            |--------------------------------------------------------------------------
            | Si se elimina el año, se eliminan automáticamente sus periodos.
            */
            $table->foreignId('anio_lectivo_id')
                ->constrained('anios_lectivos')
                ->cascadeOnDelete()
                ->comment('Referencia al año lectivo al que pertenece el periodo');

            /*
            |--------------------------------------------------------------------------
            | Número secuencial del periodo
            |--------------------------------------------------------------------------
            | Normalmente:
            | 1 = Primer periodo
            | 2 = Segundo periodo
            | 3 = Tercer periodo
            */
            $table->unsignedTinyInteger('numero')
                ->comment('Número secuencial del periodo dentro del año');

            /*
            |--------------------------------------------------------------------------
            | Nombre descriptivo
            |--------------------------------------------------------------------------
            | Ejemplo: "Primer Periodo", "Segundo Periodo"
            */
            $table->string('nombre', 100)
                ->comment('Nombre visible del periodo académico');

            /*
            |--------------------------------------------------------------------------
            | Rango de fechas del periodo
            |--------------------------------------------------------------------------
            | Deben estar dentro del rango del año lectivo.
            | Se valida en capa de aplicación.
            */
            $table->date('fecha_inicio')
                ->comment('Fecha de inicio del periodo');

            $table->date('fecha_fin')
                ->comment('Fecha de finalización del periodo');

            /*
            |--------------------------------------------------------------------------
            | Estado operativo del periodo
            |--------------------------------------------------------------------------
            | true  = Abierto (se pueden registrar/modificar notas)
            | false = Cerrado (solo lectura)
            |
            | Se reemplaza ENUM por boolean para mayor flexibilidad futura.
            */
            $table->boolean('abierto')
                ->default(true)
                ->index()
                ->comment('Indica si el periodo permite registro de notas');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Restricción de unicidad
            |--------------------------------------------------------------------------
            | Evita que un mismo año tenga dos periodos con el mismo número.
            */
            $table->unique(
                ['anio_lectivo_id', 'numero'],
                'periodo_unico_por_anio'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos');
    }
};