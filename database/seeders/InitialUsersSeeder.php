<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class InitialUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@constructora.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '3221122334'
        ]);

        // Supervisor
        User::create([
            'name' => 'Daniela Supervisor',
            'email' => 'supervisor@constructora.com',
            'password' => Hash::make('supervisor123'),
            'role' => 'supervisor',
            'phone' => '3221122335'
        ]);

        // Trabajador de ejemplo 1
        User::create([
            'name' => 'Blanca Torres',
            'email' => 'blancatrabajador@constructora.com',
            'password' => Hash::make('trabajador123'),
            'role' => 'trabajador',
            'phone' => '3221234567'
        ]);

        // Trabajador de ejemplo 2
        User::create([
            'name' => 'Fabricio Gallardo',
            'email' => 'fabriciotrabajador@constructora.com',
            'password' => Hash::make('trabajador456'),
            'role' => 'trabajador',
            'phone' => '3227654321'
        ]);

        // Trabajador de ejemplo 3
        User::create([
            'name' => 'Gilberto Perez',
            'email' => 'gilbertotrabajador@constructora.com',
            'password' => Hash::make('trabajador789'),
            'role' => 'trabajador',
            'phone' => '3221234568'
        ]);
    }
}