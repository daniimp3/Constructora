<?php

namespace App\Services;

use App\Models\{Tarea, Notificacion};
use Illuminate\Support\Facades\DB;

/**
 * SERVICIO DE TAREAS
 * 
 * Contiene la lógica de negocio relacionada con tareas,
 * asignaciones, notificaciones y verificación de vencimientos
 */
class ServicioTarea
{
    /**
     * Asignar tarea y notificar al trabajador
     * Crea una notificación para informar al trabajador de su nueva tarea
     */
    public function asignarTarea(Tarea $tarea)
    {
        Notificacion::create([
            'id_usuario' => $tarea->asignado_a,
            'tipo' => 'tarea_asignada',
            'titulo' => 'Nueva Tarea Asignada',
            'mensaje' => "Se te ha asignado la tarea: {$tarea->nombre}",
            'datos' => [
                'id_tarea' => $tarea->id,
                'id_proyecto' => $tarea->id_proyecto,
                'prioridad' => $tarea->prioridad,
                'fecha_vencimiento' => $tarea->fecha_vencimiento,
            ],
        ]);
    }

    /**
     * Verificar tareas vencidas y notificar
     * Este método debe ejecutarse diariamente (por ejemplo, con un cron job)
     * Notifica a los trabajadores sobre tareas que han vencido
     */
    public function verificarTareasVencidas()
    {
        // Buscar tareas vencidas que no estén completadas
        $tareasVencidas = Tarea::where('fecha_vencimiento', '<', now())
            ->where('estado', '!=', 'completada')
            // Evitar notificar múltiples veces: solo si no se notificó en las últimas 24 horas
            ->whereDoesntHave('asignadoA.notificaciones', function($query) {
                $query->where('tipo', 'tarea_vencida')
                    ->where('created_at', '>', now()->subDay());
            })
            ->get();

        // Crear notificación para cada tarea vencida
        foreach ($tareasVencidas as $tarea) {
            Notificacion::create([
                'id_usuario' => $tarea->asignado_a,
                'tipo' => 'tarea_vencida',
                'titulo' => 'Tarea Vencida',
                'mensaje' => "La tarea '{$tarea->nombre}' está vencida desde {$tarea->fecha_vencimiento->diffForHumans()}",
                'datos' => [
                    'id_tarea' => $tarea->id,
                    'id_proyecto' => $tarea->id_proyecto,
                ],
            ]);
        }

        return $tareasVencidas->count();
    }

    /**
     * Obtener resumen de tareas de un trabajador
     */
    public function obtenerResumenTareas($idTrabajador)
    {
        $tareas = Tarea::where('asignado_a', $idTrabajador)->get();

        return [
            'total' => $tareas->count(),
            'pendientes' => $tareas->where('estado', 'pendiente')->count(),
            'en_progreso' => $tareas->where('estado', 'en_progreso')->count(),
            'completadas' => $tareas->where('estado', 'completada')->count(),
            'vencidas' => $tareas->filter(function($tarea) {
                return $tarea->fecha_vencimiento < now() && $tarea->estado !== 'completada';
            })->count(),
        ];
    }

    /**
     * Actualizar progreso de una tarea
     * También actualiza el avance del proyecto
     */
    public function actualizarProgresoTarea(Tarea $tarea, $porcentaje)
    {
        $tarea->porcentaje_completado = $porcentaje;
        
        // Si llega al 100%, marcar como completada
        if ($porcentaje >= 100) {
            $tarea->estado = 'completada';
            $tarea->fecha_completado = now();
        }
        
        $tarea->save();

        // Actualizar el avance del proyecto
        $servicioProyecto = new ServicioProyecto();
        $servicioProyecto->actualizarAvanceProyecto($tarea->proyecto);

        return $tarea;
    }
}
