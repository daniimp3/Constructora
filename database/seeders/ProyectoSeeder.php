<?php

namespace Database\Seeders;

use App\Models\{Proyecto, Presupuesto, Tarea, Usuario};
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProyectoSeeder extends Seeder
{
    public function run()
    {
        $admin = Usuario::where('rol', 'administrador')->first();
        $supervisor = Usuario::where('rol', 'supervisor')->first();
        $trabajador = Usuario::where('rol', 'trabajador')->first();

        // ========== PROYECTO 1: EDIFICIO RESIDENCIAL ==========
        $proyecto1 = Proyecto::create([
            'nombre' => 'Edificio Residencial "Las Palmas"',
            'descripcion' => 'Construcción de edificio residencial de 4 niveles con 16 departamentos de 2 y 3 recámaras, área común con alberca y gimnasio',
            'ubicacion' => 'Av. México 1234, Col. Centro, Tepic, Nayarit',
            'fecha_inicio' => Carbon::now()->subMonths(3),
            'fecha_fin_estimada' => Carbon::now()->addMonths(9),
            'estado' => 'activo',
            'presupuesto_total' => 8500000.00,
            'id_administrador' => $admin->id,
            'porcentaje_avance' => 35.00,
        ]);

        // Presupuestos del Proyecto 1
        $presupuestos1 = [
            [
                'categoria' => 'materiales',
                'concepto' => 'Materiales de construcción estructural',
                'monto_estimado' => 3200000.00,
                'monto_actual' => 1150000.00,
            ],
            [
                'categoria' => 'mano_obra',
                'concepto' => 'Albañilería y obra civil',
                'monto_estimado' => 2800000.00,
                'monto_actual' => 980000.00,
            ],
            [
                'categoria' => 'equipos',
                'concepto' => 'Renta de maquinaria y equipo',
                'monto_estimado' => 950000.00,
                'monto_actual' => 340000.00,
            ],
            [
                'categoria' => 'materiales',
                'concepto' => 'Acabados e instalaciones',
                'monto_estimado' => 1200000.00,
                'monto_actual' => 85000.00,
            ],
            [
                'categoria' => 'otros',
                'concepto' => 'Permisos y gastos administrativos',
                'monto_estimado' => 350000.00,
                'monto_actual' => 280000.00,
            ],
        ];

        foreach ($presupuestos1 as $presupuesto) {
            $proyecto1->presupuestos()->create($presupuesto);
        }

        // Tareas del Proyecto 1
        $tareas1 = [
            [
                'nombre' => 'Excavación y cimentación',
                'descripcion' => 'Excavación del terreno y construcción de zapatas y cimientos',
                'estado' => 'completada',
                'prioridad' => 'alta',
                'fecha_inicio' => Carbon::now()->subMonths(3),
                'fecha_vencimiento' => Carbon::now()->subMonths(2)->subWeeks(2),
                'fecha_completada' => Carbon::now()->subMonths(2)->subWeeks(2),
                'asignado_a' => $trabajador->id,
                'creado_por' => $supervisor->id,
                'porcentaje_completado' => 100.00,
            ],
            [
                'nombre' => 'Estructura nivel 1',
                'descripcion' => 'Construcción de columnas, trabes y losa del primer nivel',
                'estado' => 'completada',
                'prioridad' => 'alta',
                'fecha_inicio' => Carbon::now()->subMonths(2)->subWeeks(2),
                'fecha_vencimiento' => Carbon::now()->subMonths(1)->subWeeks(3),
                'fecha_completada' => Carbon::now()->subMonths(1)->subWeeks(3),
                'asignado_a' => $trabajador->id,
                'creado_por' => $supervisor->id,
                'porcentaje_completado' => 100.00,
            ],
            [
                'nombre' => 'Estructura nivel 2',
                'descripcion' => 'Construcción de columnas, trabes y losa del segundo nivel',
                'estado' => 'en_progreso',
                'prioridad' => 'alta',
                'fecha_inicio' => Carbon::now()->subMonths(1)->subWeeks(2),
                'fecha_vencimiento' => Carbon::now()->addWeeks(2),
                'asignado_a' => $trabajador->id,
                'creado_por' => $supervisor->id,
                'porcentaje_completado' => 65.00,
            ],
            [
                'nombre' => 'Instalación eléctrica nivel 1',
                'descripcion' => 'Instalación de tuberías y cableado eléctrico',
                'estado' => 'en_progreso',
                'prioridad' => 'media',
                'fecha_inicio' => Carbon::now()->subWeeks(3),
                'fecha_vencimiento' => Carbon::now()->addWeeks(3),
                'asignado_a' => $trabajador->id,
                'creado_por' => $supervisor->id,
                'porcentaje_completado' => 40.00,
            ],
            [
                'nombre' => 'Instalación hidráulica nivel 1',
                'descripcion' => 'Instalación de tuberías de agua potable y drenaje',
                'estado' => 'pendiente',
                'prioridad' => 'media',
                'fecha_inicio' => Carbon::now()->addWeeks(1),
                'fecha_vencimiento' => Carbon::now()->addWeeks(4),
                'asignado_a' => $trabajador->id,
                'creado_por' => $supervisor->id,
                'porcentaje_completado' => 0.00,
            ],
        ];

        foreach ($tareas1 as $tarea) {
            $proyecto1->tareas()->create($tarea);
        }

        // ========== PROYECTO 2: CASA HABITACIÓN ==========
        $proyecto2 = Proyecto::create([
            'nombre' => 'Casa Habitación Moderna "Vista Hermosa"',
            'descripcion' => 'Construcción de casa habitación de 2 plantas, 3 recámaras, 2.5 baños, sala, comedor, cocina integral y jardín',
            'ubicacion' => 'Fraccionamiento Vista Hermosa Lote 45, Tepic, Nayarit',
            'fecha_inicio' => Carbon::now()->subMonths(2),
            'fecha_fin_estimada' => Carbon::now()->addMonths(6),
            'estado' => 'activo',
            'presupuesto_total' => 2800000.00,
            'id_administrador' => $admin->id,
            'porcentaje_avance' => 45.00,
        ]);

        $presupuestos2 = [
            [
                'categoria' => 'materiales',
                'concepto' => 'Materiales estructurales y acabados',
                'monto_estimado' => 1400000.00,
                'monto_actual' => 650000.00,
            ],
            [
                'categoria' => 'mano_obra',
                'concepto' => 'Construcción y acabados',
                'monto_estimado' => 1000000.00,
                'monto_actual' => 450000.00,
            ],
            [
                'categoria' => 'equipos',
                'concepto' => 'Renta de equipo',
                'monto_estimado' => 250000.00,
                'monto_actual' => 110000.00,
            ],
            [
                'categoria' => 'otros',
                'concepto' => 'Gastos varios',
                'monto_estimado' => 150000.00,
                'monto_actual' => 95000.00,
            ],
        ];

        foreach ($presupuestos2 as $presupuesto) {
            $proyecto2->presupuestos()->create($presupuesto);
        }

        // ========== PROYECTO 3: TERMINADO ==========
        $proyecto3 = Proyecto::create([
            'nombre' => 'Remodelación Oficinas "Centro Empresarial"',
            'descripcion' => 'Remodelación completa de oficinas corporativas en 3er piso, incluye pisos, plafones, pintura, iluminación y mobiliario',
            'ubicacion' => 'Av. Insurgentes 567, Col. Centro, Tepic, Nayarit',
            'fecha_inicio' => Carbon::now()->subMonths(5),
            'fecha_fin_estimada' => Carbon::now()->subMonth(1),
            'fecha_fin_real' => Carbon::now()->subWeeks(2),
            'estado' => 'terminado',
            'presupuesto_total' => 950000.00,
            'id_administrador' => $admin->id,
            'porcentaje_avance' => 100.00,
        ]);

        $presupuestos3 = [
            [
                'categoria' => 'materiales',
                'concepto' => 'Materiales de remodelación',
                'monto_estimado' => 450000.00,
                'monto_actual' => 465000.00,
            ],
            [
                'categoria' => 'mano_obra',
                'concepto' => 'Mano de obra especializada',
                'monto_estimado' => 380000.00,
                'monto_actual' => 375000.00,
            ],
            [
                'categoria' => 'otros',
                'concepto' => 'Gastos generales',
                'monto_estimado' => 120000.00,
                'monto_actual' => 110000.00,
            ],
        ];

        foreach ($presupuestos3 as $presupuesto) {
            $proyecto3->presupuestos()->create($presupuesto);
        }

        $this->command->info('✅ Proyectos creados exitosamente');
        $this->command->info('📊 3 proyectos con presupuestos y tareas');
    }
}

