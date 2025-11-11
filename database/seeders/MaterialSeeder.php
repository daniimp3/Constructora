<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run()
    {
        $materiales = [
            [
                'nombre' => 'Cemento Portland CPC 30R',
                'descripcion' => 'Cemento gris tipo CPC 30R para uso general',
                'unidad' => 'bulto 50kg',
                'costo_unitario' => 185.00,
                'existencia' => 250,
                'existencia_minima' => 50,
            ],
            [
                'nombre' => 'Arena de Río Lavada',
                'descripcion' => 'Arena de río cernida y lavada',
                'unidad' => 'm3',
                'costo_unitario' => 350.00,
                'existencia' => 45,
                'existencia_minima' => 10,
            ],
            [
                'nombre' => 'Grava 3/4"',
                'descripcion' => 'Grava triturada de 3/4 de pulgada',
                'unidad' => 'm3',
                'costo_unitario' => 380.00,
                'existencia' => 35,
                'existencia_minima' => 8,
            ],
            [
                'nombre' => 'Varilla Corrugada 3/8"',
                'descripcion' => 'Varilla de acero corrugada grado 42',
                'unidad' => 'pieza 12m',
                'costo_unitario' => 95.00,
                'existencia' => 500,
                'existencia_minima' => 100,
            ],
            [
                'nombre' => 'Varilla Corrugada 1/2"',
                'descripcion' => 'Varilla de acero corrugada grado 42',
                'unidad' => 'pieza 12m',
                'costo_unitario' => 145.00,
                'existencia' => 400,
                'existencia_minima' => 80,
            ],
            [
                'nombre' => 'Varilla Corrugada 5/8"',
                'descripcion' => 'Varilla de acero corrugada grado 42',
                'unidad' => 'pieza 12m',
                'costo_unitario' => 215.00,
                'existencia' => 300,
                'existencia_minima' => 60,
            ],
            [
                'nombre' => 'Block de Concreto 15x20x40',
                'descripcion' => 'Block hueco estándar',
                'unidad' => 'pieza',
                'costo_unitario' => 12.50,
                'existencia' => 3000,
                'existencia_minima' => 500,
            ],
            [
                'nombre' => 'Ladrillo Rojo Recocido',
                'descripcion' => 'Ladrillo tipo tabique rojo',
                'unidad' => 'millar',
                'costo_unitario' => 5500.00,
                'existencia' => 15,
                'existencia_minima' => 3,
            ],
            [
                'nombre' => 'Alambre Recocido Cal. 18',
                'descripcion' => 'Alambre para amarre',
                'unidad' => 'kg',
                'costo_unitario' => 28.00,
                'existencia' => 150,
                'existencia_minima' => 30,
            ],
            [
                'nombre' => 'Cal Hidratada',
                'descripcion' => 'Cal hidratada para morteros',
                'unidad' => 'bulto 20kg',
                'costo_unitario' => 75.00,
                'existencia' => 180,
                'existencia_minima' => 40,
            ],
            [
                'nombre' => 'Yeso en Polvo',
                'descripcion' => 'Yeso para acabados interiores',
                'unidad' => 'bulto 25kg',
                'costo_unitario' => 95.00,
                'existencia' => 120,
                'existencia_minima' => 25,
            ],
            [
                'nombre' => 'Pintura Vinílica Blanca',
                'descripcion' => 'Pintura lavable para interiores',
                'unidad' => 'cubeta 19L',
                'costo_unitario' => 580.00,
                'existencia' => 25,
                'existencia_minima' => 5,
            ],
            [
                'nombre' => 'Impermeabilizante Acrílico',
                'descripcion' => 'Impermeabilizante para techos y azoteas',
                'unidad' => 'cubeta 19L',
                'costo_unitario' => 850.00,
                'existencia' => 15,
                'existencia_minima' => 3,
            ],
            [
                'nombre' => 'Tubería PVC 4" Sanitaria',
                'descripcion' => 'Tubería para desagüe',
                'unidad' => 'tubo 6m',
                'costo_unitario' => 280.00,
                'existencia' => 60,
                'existencia_minima' => 15,
            ],
            [
                'nombre' => 'Tubería PVC 1/2" Hidráulica',
                'descripcion' => 'Tubería para agua potable',
                'unidad' => 'tubo 6m',
                'costo_unitario' => 85.00,
                'existencia' => 100,
                'existencia_minima' => 20,
            ],
            [
                'nombre' => 'Cable Calibre 12 THW',
                'descripcion' => 'Cable eléctrico para instalaciones',
                'unidad' => 'rollo 100m',
                'costo_unitario' => 1250.00,
                'existencia' => 30,
                'existencia_minima' => 5,
            ],
            [
                'nombre' => 'Clavos 2.5"',
                'descripcion' => 'Clavos para construcción',
                'unidad' => 'kg',
                'costo_unitario' => 32.00,
                'existencia' => 200,
                'existencia_minima' => 40,
            ],
            [
                'nombre' => 'Malla Electrosoldada 6x6-10/10',
                'descripcion' => 'Malla para firmes y losas',
                'unidad' => 'rollo',
                'costo_unitario' => 850.00,
                'existencia' => 25,
                'existencia_minima' => 5,
            ],
        ];

        foreach ($materiales as $material) {
            Material::create($material);
        }

        $this->command->info('✅ Materiales creados exitosamente');
    }
}
