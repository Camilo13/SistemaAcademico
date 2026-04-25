<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nombre'         => 'Juan',
            'apellidos'      => 'Pérez López',
            'identificacion' => '12345',
            'correo'         => 'admin@example.com',
            'ubicacion'      => 'Bogotá',
            'contacto'       => '3001234567',
            'password'       => 'admin123',
            'rol'            => 'administrador',
            'activo'         => true,
        ]);

        User::create([
            'nombre'         => 'María',
            'apellidos'      => 'Rodríguez Gómez',
            'identificacion' => '123456',
            'correo'         => 'docente@example.com',
            'ubicacion'      => 'Medellín',
            'contacto'       => '3017654321',
            'password'       => 'docente001',
            'rol'            => 'docente',
            'activo'         => true,
        ]);

        User::create([
            'nombre'         => 'Pedro',
            'apellidos'      => 'Martínez Torres',
            'identificacion' => '1234567',
            'correo'         => 'estudiante@example.com',
            'ubicacion'      => 'Cali',
            'contacto'       => '3029876543',
            'password'       => 'estudiante001',
            'rol'            => 'estudiante',
            'activo'         => true,
        ]);
    }
}
