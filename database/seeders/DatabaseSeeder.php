<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecutar todos los seeders
     */
    public function run()
    {
        $this->call([
            UsuarioSeeder::class,
            MaterialSeeder::class,
            ProyectoSeeder::class,
        ]);
    }
}