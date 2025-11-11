<?php

namespace App\Services;

use App\Models\{Proyecto, Notificacion};
use Illuminate\Support\Facades\DB;

/**
 * SERVICIO DE PROYECTO
 * 
 * Contiene la lógica de negocio relacionada con proyectos,
 * estadísticas, avances y gestión de riesgos
 */
class ServicioProyecto
{
    /**
     * Actualizar el porcentaje de avance del proyecto
     * Calcula el avance basándose en las tareas completadas
     */
    public function actualizarAvanceProyecto(Proyecto $proyecto)
    {
        $totalTareas = $proyecto->tareas()->count();

        // Si no hay tareas, el avance es 0%
        if ($totalTareas === 0) {
            $proyecto->porcentaje_avance = 0;
            $proyecto->save();
            return;
        }

        // Calcular porcentaje basado en tareas completadas
        $tareasCompletadas = $proyecto->tareas()->where('estado', 'completada')->count();
        $porcentaje = ($tareasCompletadas / $totalTareas) * 100;

        $proyecto->porcentaje_avance = round($porcentaje, 2);
        $proyecto->save();

        // Notificar si el proyecto se completa al 100%
        if ($porcentaje >= 100 && $proyecto->estado === 'activo') {
            $this->notificarProyectoCompletado($proyecto);
        }
    }

    /**
     * Notificar al administrador cuando un proyecto se completa
     */
    private function notificarProyectoCompletado(Proyecto $proyecto)
    {
        Notificacion::create([
            'id_usuario' => $proyecto->id_administrador,
            'tipo' => 'proyecto_completado',
            'titulo' => 'Proyecto Completado',
            'mensaje' => "¡Felicidades! El proyecto '{$proyecto->nombre}' ha sido completado al 100%",
            'datos' => [
                'id_proyecto' => $proyecto->id,
            ],
        ]);
    }

    /**
     * Obtener estadísticas completas del proyecto
     * Incluye información financiera, de tareas, incidencias y avance
     */
    public function obtenerEstadisticasProyecto(Proyecto $proyecto)
    {
        return [
            // Estadísticas financieras
            'financiero' => [
                'presupuesto' => $proyecto->presupuesto_total,
                'gastos' => $proyecto->obtenerTotalGastos(),
                'variacion' => $proyecto->obtenerVariacionPresupuesto(),
                'porcentaje_variacion' => ($proyecto->obtenerTotalGastos() / $proyecto->presupuesto_total) * 100,
            ],
            
            // Estadísticas de tareas
            'tareas' => [
                'total' => $proyecto->tareas()->count(),
                'completadas' => $proyecto->tareas()->completadas()->count(),
                'en_progreso' => $proyecto->tareas()->enProgreso()->count(),
                'pendientes' => $proyecto->tareas()->pendientes()->count(),
                'vencidas' => $proyecto->tareas()
                    ->where('fecha_vencimiento', '<', now())
                    ->where('estado', '!=', 'completada')
                    ->count(),
            ],
            
            // Estadísticas de incidencias
            'incidencias' => [
                'total' => $proyecto->incidencias()->count(),
                'abiertas' => $proyecto->incidencias()->abiertas()->count(),
                'criticas' => $proyecto->incidencias()->criticas()->count(),
            ],
            
            // Información de avance y tiempo
            'avance' => [
                'porcentaje' => $proyecto->porcentaje_avance,
                'fecha_inicio' => $proyecto->fecha_inicio,
                'fecha_fin_estimada' => $proyecto->fecha_fin_estimada,
                'dias_restantes' => now()->diffInDays($proyecto->fecha_fin_estimada, false),
            ],
        ];
    }

    /**
     * Verificar si el proyecto está en riesgo
     * Analiza diferentes factores: presupuesto, retrasos, tareas e incidencias
     */
    public function verificarRiesgoProyecto(Proyecto $proyecto)
    {
        $riesgos = [];

        // RIESGO POR PRESUPUESTO
        $usoPresupuesto = ($proyecto->obtenerTotalGastos() / $proyecto->presupuesto_total) * 100;
        if ($usoPresupuesto > 90) {
            $riesgos[] = [
                'tipo' => 'presupuesto',
                'severidad' => $usoPresupuesto > 100 ? 'alta' : 'media',
                'mensaje' => "Uso de presupuesto: {$usoPresupuesto}%",
            ];
        }

        // RIESGO POR RETRASO
        if ($proyecto->fecha_fin_estimada < now() && $proyecto->estado === 'activo') {
            $diasRetrasados = now()->diffInDays($proyecto->fecha_fin_estimada);
            $riesgos[] = [
                'tipo' => 'retraso',
                'severidad' => 'alta',
                'mensaje' => "Proyecto retrasado {$diasRetrasados} días",
            ];
        }

        // RIESGO POR TAREAS VENCIDAS
        $tareasVencidas = $proyecto->tareas()
            ->where('fecha_vencimiento', '<', now())
            ->where('estado', '!=', 'completada')
            ->count();

        if ($tareasVencidas > 5) {
            $riesgos[] = [
                'tipo' => 'tareas',
                'severidad' => 'media',
                'mensaje' => "{$tareasVencidas} tareas vencidas",
            ];
        }

        // RIESGO POR INCIDENCIAS CRÍTICAS
        $incidenciasCriticas = $proyecto->incidencias()->criticas()->abiertas()->count();
        if ($incidenciasCriticas > 0) {
            $riesgos[] = [
                'tipo' => 'incidencias',
                'severidad' => 'alta',
                'mensaje' => "{$incidenciasCriticas} incidencias críticas sin resolver",
            ];
        }

        return $riesgos;
    }
}
