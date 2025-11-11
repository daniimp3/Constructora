<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        // ========== ADMINISTRADORES ==========
        Usuario::create([
            'nombre' => 'Carlos Rodríguez',
            'email' => 'admin@recursamos.com',
            'contraseña' => Hash::make('password123'),
            'rol' => 'administrador',
            'telefono' => '+52 311 123 4567',
            'esta_activo' => true,
        ]);

        Usuario::create([
            'nombre' => 'María González',
            'email' => 'maria.admin@recursamos.com',
            'contraseña' => Hash::make('password123'),
            'rol' => 'administrador',
            'telefono' => '+52 311 234 5678',
            'esta_activo' => true,
        ]);

        // ========== SUPERVISORES ==========
        Usuario::create([
            'nombre' => 'José Martínez',
            'email' => 'jose.supervisor@recursamos.com',
            'contraseña' => Hash::make('password123'),
            'rol' => 'supervisor',
            'telefono' => '+52 311 345 6789',
            'esta_activo' => true,
        ]);

        Usuario::create([
            'nombre' => 'Ana López',
            'email' => 'ana.supervisor@recursamos.com',
            'contraseña' => Hash::make('password123'),
            'rol' => 'supervisor',
            'telefono' => '+52 311 456 7890',
            'esta_activo' => true,
        ]);

        Usuario::create([
            'nombre' => 'Roberto Sánchez',
            'email' => 'roberto.supervisor@recursamos.com',
            'contraseña' => Hash::make('password123'),
            'rol' => 'supervisor',
            'telefono' => '+52 311 567 8901',
            'esta_activo' => true,
        ]);

        // ========== TRABAJADORES ==========
        $trabajadores = [
            ['nombre' => 'Pedro Hernández', 'email' => 'pedro.t@recursamos.com'],
            ['nombre' => 'Luis Ramírez', 'email' => 'luis.t@recursamos.com'],
            ['nombre' => 'Miguel Torres', 'email' => 'miguel.t@recursamos.com'],
            ['nombre' => 'Jorge Flores', 'email' => 'jorge.t@recursamos.com'],
            ['nombre' => 'Fernando Díaz', 'email' => 'fernando.t@recursamos.com'],
            ['nombre' => 'Antonio Cruz', 'email' => 'antonio.t@recursamos.com'],
            ['nombre' => 'Juan Mendoza', 'email' => 'juan.t@recursamos.com'],
            ['nombre' => 'Ricardo Vargas', 'email' => 'ricardo.t@recursamos.com'],
            ['nombre' => 'Daniel Morales', 'email' => 'daniel.t@recursamos.com'],
            ['nombre' => 'Alberto Ruiz', 'email' => 'alberto.t@recursamos.com'],
        ];

        foreach ($trabajadores as $trabajador) {
            Usuario::create([
                'nombre' => $trabajador['nombre'],
                'email' => $trabajador['email'],
                'contraseña' => Hash::make('password123'),
                'rol' => 'trabajador',
                'telefono' => '+52 311 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                'esta_activo' => true,
            ]);
        }

        $this->command->info('✅ Usuarios creados exitosamente');
        $this->command->info('📧 Email: admin@recursamos.com | 🔑 Password: password123');
    }
}
