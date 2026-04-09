<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BibliotecaMateria;
use App\Models\Recurso;

class RecursoSeeder extends Seeder
{
    public function run(): void
    {
        $materiaPerros = BibliotecaMateria::where('nombre', 'Cuidado y Comportamiento Canino')->first();
        $materiaGatos  = BibliotecaMateria::where('nombre', 'Salud y Bienestar Felino')->first();

        if (!$materiaPerros || !$materiaGatos) {
            return;
        }

        /* ===============================
           RECURSOS · PERROS
        =============================== */
        Recurso::insert([
            [
                'id_materia' => $materiaPerros->id_materia,
                'titulo' => 'Lenguaje corporal en perros',
                'descripcion' => 'Cómo entender las señales de tu perro',
                'tipo' => 'video',
                'origen' => 'url',
                'url' => 'https://www.youtube.com/watch?v=Z4Fq0L0H0mU',
                'mime_type' => null,
                'autor' => 'Canal Veterinario',
                'visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_materia' => $materiaPerros->id_materia,
                'titulo' => 'Entrenamiento básico para perros',
                'descripcion' => 'Órdenes básicas y refuerzo positivo',
                'tipo' => 'video',
                'origen' => 'url',
                'url' => 'https://www.youtube.com/watch?v=O9q8ZpKc9yM',
                'mime_type' => null,
                'autor' => 'Dog Training',
                'visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_materia' => $materiaPerros->id_materia,
                'titulo' => 'Cuidados esenciales del perro',
                'descripcion' => 'Salud, alimentación y ejercicio',
                'tipo' => 'video',
                'origen' => 'url',
                'url' => 'https://www.youtube.com/watch?v=4JZ9f7xZ0jE',
                'mime_type' => null,
                'autor' => 'Mundo Canino',
                'visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /* ===============================
           RECURSOS · GATOS
        =============================== */
        Recurso::insert([
            [
                'id_materia' => $materiaGatos->id_materia,
                'titulo' => 'Lenguaje corporal de los gatos',
                'descripcion' => 'Aprende a interpretar a tu gato',
                'tipo' => 'video',
                'origen' => 'url',
                'url' => 'https://www.youtube.com/watch?v=J3l9rXj8zYQ',
                'mime_type' => null,
                'autor' => 'Cat Behavior',
                'visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_materia' => $materiaGatos->id_materia,
                'titulo' => 'Cuidados básicos del gato',
                'descripcion' => 'Higiene, alimentación y salud',
                'tipo' => 'video',
                'origen' => 'url',
                'url' => 'https://www.youtube.com/watch?v=VQkF7EJ8sJk',
                'mime_type' => null,
                'autor' => 'Mundo Felino',
                'visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_materia' => $materiaGatos->id_materia,
                'titulo' => 'Enriquecimiento ambiental para gatos',
                'descripcion' => 'Cómo evitar el estrés felino',
                'tipo' => 'video',
                'origen' => 'url',
                'url' => 'https://www.youtube.com/watch?v=976aIsyZa9I&list=LL&index=5',
                'mime_type' => null,
                'autor' => 'Veterinaria Online',
                'visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
