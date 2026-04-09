<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BibliotecaMateria;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        BibliotecaMateria::insert([
            [
                'nombre' => 'Cuidado y Comportamiento Canino',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Salud y Bienestar Felino',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
