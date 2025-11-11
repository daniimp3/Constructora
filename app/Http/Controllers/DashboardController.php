<?php

namespace App\Http\Controllers;

use App\Models\{Proyecto, Tarea, Incidencia, AlertaPresupuesto, Notificacion};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// ============================================
// DASHBOARD CONTROLLER
// ============================================
class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        
        $datos = match($usuario->rol) {
            'administrador' => $this->obtenerDashboardAdmin($usuario),
            'supervisor' => $this->obtenerDashboardSupervisor($usuario),
            'trabajador' => $this->obtenerDashboardTrabajador($usuario),
            default => abort(403)
        };

        return view('dashboard', $datos);
    }

    private function obtenerDashboardAdmin($usuario)
    {
        $proyectos = Proyecto::where('id_administrador', $usuario->id)->get();
        $idsProyectos = $proyectos->pluck('id');

        return [
            // Estadísticas generales
            'total_proyectos' => $proyectos->count(),
            'proyectos_activos' => $proyectos->where('estado', 'activo')->count(),
            'proyectos_terminados' => $proyectos->where('estado', 'terminado')->count(),
            
            // Financiero
            'presupuesto_total' => $proyectos->sum('presupuesto_total'),
            'gastos_totales' => DB::table('gastos')
                ->whereIn('id_proyecto', $idsProyectos)
                ->sum('monto'),
            
            // Alertas y notificaciones
            'alertas_presupuesto' => AlertaPresupuesto::whereIn('id_proyecto', $idsProyectos)
                ->where('fue_leida', false)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            
            'incidencias_criticas' => Incidencia::whereIn('id_proyecto', $idsProyectos)
                ->where('severidad', 'critica')
                ->where('estado', '!=', 'cerrado')
                ->count(),
            
            'notificaciones_no_leidas' => Notificacion::where('id_usuario', $usuario->id)
                ->where('fue_leida', false)
                ->count(),
            
            // Proyectos recientes
            'proyectos_recientes' => $proyectos->sortByDesc('updated_at')->take(5),
            
            // Gráfico de avance
            'avance_proyectos' => $proyectos->map(fn($p) => [
                'nombre' => $p->nombre,
                'avance' => $p->porcentaje_avance,
            ]),
            
            // Gastos por categoría
            'gastos_por_categoria' => DB::table('gastos')
                ->whereIn('id_proyecto', $idsProyectos)
                ->select('categoria', DB::raw('SUM(monto) as total'))
                ->groupBy('categoria')
                ->get(),
            
            // Proyectos con retraso
            'proyectos_retrasados' => $proyectos->filter(function($proyecto) {
                return $proyecto->fecha_fin_estimada < now() 
                    && $proyecto->estado === 'activo';
            })->count(),
        ];
    }

    private function obtenerDashboardSupervisor($usuario)
    {
        $proyectosAsignados = Proyecto::whereHas('tareas', function($query) use ($usuario) {
            $query->where('creado_por', $usuario->id);
        })->get();

        $idsProyectos = $proyectosAsignados->pluck('id');

        return [
            'proyectos_asignados' => $proyectosAsignados->count(),
            
            'mis_tareas' => Tarea::where('creado_por', $usuario->id)->count(),
            
            'tareas_pendientes' => Tarea::whereIn('id_proyecto', $idsProyectos)
                ->where('estado', 'pendiente')
                ->count(),
            
            'tareas_en_progreso' => Tarea::whereIn('id_proyecto', $idsProyectos)
                ->where('estado', 'en_progreso')
                ->count(),
            
            'tareas_vencidas' => Tarea::whereIn('id_proyecto', $idsProyectos)
                ->where('fecha_vencimiento', '<', now())
                ->where('estado', '!=', 'completada')
                ->count(),
            
            'avances_recientes' => DB::table('avances_obra')
                ->where('id_supervisor', $usuario->id)
                ->orderBy('fecha_avance', 'desc')
                ->limit(5)
                ->get(),
            
            'asistencia_hoy' => DB::table('asistencias')
                ->whereIn('id_proyecto', $idsProyectos)
                ->whereDate('fecha_asistencia', today())
                ->count(),
            
            'incidencias_abiertas' => Incidencia::whereIn('id_proyecto', $idsProyectos)
                ->whereIn('estado', ['reportado', 'en_revision'])
                ->count(),
            
            'proyectos' => $proyectosAsignados,
            
            'notificaciones_no_leidas' => Notificacion::where('id_usuario', $usuario->id)
                ->where('fue_leida', false)
                ->count(),
        ];
    }

    private function obtenerDashboardTrabajador($usuario)
    {
        $misTareas = Tarea::where('asignado_a', $usuario->id)->get();
        $idsProyectos = $misTareas->pluck('id_proyecto')->unique();

        return [
            'total_tareas' => $misTareas->count(),
            
            'tareas_pendientes' => $misTareas->where('estado', 'pendiente')->count(),
            
            'tareas_en_progreso' => $misTareas->where('estado', 'en_progreso')->count(),
            
            'tareas_completadas' => $misTareas->where('estado', 'completada')->count(),
            
            'tareas_vencidas' => $misTareas->filter(function($tarea) {
                return $tarea->fecha_vencimiento < now() && $tarea->estado !== 'completada';
            })->count(),
            
            'tareas_hoy' => $misTareas->filter(function($tarea) {
                return $tarea->fecha_vencimiento && $tarea->fecha_vencimiento->isToday();
            }),
            
            'tareas_semana' => $misTareas->filter(function($tarea) {
                return $tarea->fecha_vencimiento && $tarea->fecha_vencimiento->isCurrentWeek();
            }),
            
            'proyectos_activos' => Proyecto::whereIn('id', $idsProyectos)
                ->where('estado', 'activo')
                ->get(),
            
            'mi_asistencia_mes' => DB::table('asistencias')
                ->where('id_usuario', $usuario->id)
                ->whereMonth('fecha_asistencia', now()->month)
                ->count(),
            
            'notificaciones_no_leidas' => Notificacion::where('id_usuario', $usuario->id)
                ->where('fue_leida', false)
                ->count(),
        ];
    }
}

// ============================================
// NOTIFICATION CONTROLLER
// ============================================
class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::where('id_usuario', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarComoLeida(Notificacion $notificacion)
    {
        $this->authorize('update', $notificacion);
        
        $notificacion->marcarComoLeida();

        return back()->with('success', 'Notificación marcada como leída');
    }

    public function marcarTodasComoLeidas()
    {
        Notificacion::where('id_usuario', Auth::id())
            ->where('fue_leida', false)
            ->update([
                'fue_leida' => true,
                'leida_en' => now(),
            ]);

        return back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }
}

// ============================================
// SERVICES - LÓGICA DE NEGOCIO
// ============================================

namespace App\Services;

use App\Models\{Proyecto, Presupuesto, AlertaPresupuesto, Notificacion};
use Illuminate\Support\Facades\DB;

class ServicioPresupuesto
{
    /**
     * Actualizar el monto actual de un presupuesto
     */
    public function actualizarMontoPresupuesto(Presupuesto $presupuesto)
    {
        $presupuesto->monto_actual = $presupuesto->gastos()->sum('monto');
        $presupuesto->save();

        // Verificar alertas
        $this->verificarAlertasPresupuesto($presupuesto);
    }

    /**
     * Verificar y crear alertas de presupuesto
     */
    public function verificarAlertasPresupuesto(Presupuesto $presupuesto)
    {
        $porcentaje = $presupuesto->obtenerPorcentajeVariacion();
        $proyecto = $presupuesto->proyecto;

        // Alerta al 90%
        if ($porcentaje >= 90 && $porcentaje < 100) {
            $this->crearAlerta($proyecto, $presupuesto, 90, $porcentaje, 'advertencia');
        }

        // Alerta al 100% (excedido)
        if ($porcentaje >= 100) {
            $this->crearAlerta($proyecto, $presupuesto, 100, $porcentaje, 'excedido');
            $this->notificarAdministrador($proyecto, $presupuesto, $porcentaje);
        }
    }

    private function crearAlerta($proyecto, $presupuesto, $umbral, $actual, $tipo)
    {
        // Evitar duplicados
        $existe = AlertaPresupuesto::where('id_presupuesto', $presupuesto->id)
            ->where('porcentaje_umbral', $umbral)
            ->where('fue_leida', false)
            ->exists();

        if (!$existe) {
            AlertaPresupuesto::create([
                'id_proyecto' => $proyecto->id,
                'id_presupuesto' => $presupuesto->id,
                'porcentaje_umbral' => $umbral,
                'porcentaje_actual' => $actual,
                'tipo_alerta' => $tipo,
                'mensaje' => $this->obtenerMensajeAlerta($presupuesto, $actual, $tipo),
            ]);
        }
    }

    private function obtenerMensajeAlerta($presupuesto, $porcentaje, $tipo)
    {
        if ($tipo === 'advertencia') {
            return "⚠️ El presupuesto '{$presupuesto->concepto}' ha alcanzado el {$porcentaje}% de utilización";
        }

        return "🚨 ¡ALERTA! El presupuesto '{$presupuesto->concepto}' ha excedido el límite ({$porcentaje}%)";
    }

    private function notificarAdministrador($proyecto, $presupuesto, $porcentaje)
    {
        Notificacion::create([
            'id_usuario' => $proyecto->id_administrador,
            'tipo' => 'presupuesto_excedido',
            'titulo' => 'Presupuesto Excedido',
            'mensaje' => "El presupuesto '{$presupuesto->concepto}' del proyecto '{$proyecto->nombre}' ha excedido el límite ({$porcentaje}%)",
            'datos' => [
                'id_proyecto' => $proyecto->id,
                'id_presupuesto' => $presupuesto->id,
                'porcentaje' => $porcentaje,
            ],
        ]);
    }

    /**
     * Generar reporte de variación de presupuesto
     */
    public function generarReporteVariacion(Proyecto $proyecto)
    {
        return $proyecto->presupuestos->map(function($presupuesto) {
            return [
                'categoria' => $presupuesto->categoria,
                'concepto' => $presupuesto->concepto,
                'estimado' => $presupuesto->monto_estimado,
                'actual' => $presupuesto->monto_actual,
                'variacion' => $presupuesto->obtenerVariacion(),
                'porcentaje_variacion' => $presupuesto->obtenerPorcentajeVariacion(),
                'estado' => $this->obtenerEstadoVariacion($presupuesto->obtenerPorcentajeVariacion()),
            ];
        });
    }

    private function obtenerEstadoVariacion($porcentaje)
    {
        if ($porcentaje < 80) return 'excelente';
        if ($porcentaje < 90) return 'bueno';
        if ($porcentaje < 100) return 'alerta';
        return 'excedido';
    }
}

class ServicioProyecto
{
    /**
     * Actualizar el porcentaje de avance del proyecto
     */
    public function actualizarAvanceProyecto(Proyecto $proyecto)
    {
        $totalTareas = $proyecto->tareas()->count();

        if ($totalTareas === 0) {
            $proyecto->porcentaje_avance = 0;
            $proyecto->save();
            return;
        }

        $tareasCompletadas = $proyecto->tareas()->where('estado', 'completada')->count();
        $porcentaje = ($tareasCompletadas / $totalTareas) * 100;

        $proyecto->porcentaje_avance = round($porcentaje, 2);
        $proyecto->save();

        // Notificar si se completa el proyecto
        if ($porcentaje >= 100 && $proyecto->estado === 'activo') {
            $this->notificarProyectoCompletado($proyecto);
        }
    }

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
     * Obtener estadísticas del proyecto
     */
    public function obtenerEstadisticasProyecto(Proyecto $proyecto)
    {
        return [
            'financiero' => [
                'presupuesto' => $proyecto->presupuesto_total,
                'gastos' => $proyecto->obtenerTotalGastos(),
                'variacion' => $proyecto->obtenerVariacionPresupuesto(),
                'porcentaje_variacion' => ($proyecto->obtenerTotalGastos() / $proyecto->presupuesto_total) * 100,
            ],
            'tareas' => [
                'total' => $proyecto->tareas()->count(),
                'completadas' => $proyecto->tareas()->completadas()->count(),
                'en_progreso' => $proyecto->tareas()->enProgreso()->count(),
                'pendientes' => $proyecto->tareas()->pendientes()->count(),
                'vencidas' => $proyecto->tareas()->where('fecha_vencimiento', '<', now())
                    ->where('estado', '!=', 'completada')->count(),
            ],
            'incidencias' => [
                'total' => $proyecto->incidencias()->count(),
                'abiertas' => $proyecto->incidencias()->abiertas()->count(),
                'criticas' => $proyecto->incidencias()->criticas()->count(),
            ],
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
     */
    public function verificarRiesgoProyecto(Proyecto $proyecto)
    {
        $riesgos = [];

        // Riesgo por presupuesto
        $usoPresupuesto = ($proyecto->obtenerTotalGastos() / $proyecto->presupuesto_total) * 100;
        if ($usoPresupuesto > 90) {
            $riesgos[] = [
                'tipo' => 'presupuesto',
                'severidad' => $usoPresupuesto > 100 ? 'alta' : 'media',
                'mensaje' => "Uso de presupuesto: {$usoPresupuesto}%",
            ];
        }

        // Riesgo por retraso
        if ($proyecto->fecha_fin_estimada < now() && $proyecto->estado === 'activo') {
            $diasRetrasados = now()->diffInDays($proyecto->fecha_fin_estimada);
            $riesgos[] = [
                'tipo' => 'retraso',
                'severidad' => 'alta',
                'mensaje' => "Proyecto retrasado {$diasRetrasados} días",
            ];
        }

        // Riesgo por tareas pendientes
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

        // Riesgo por incidencias críticas
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

class ServicioTarea
{
    /**
     * Asignar tarea y notificar al trabajador
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
     */
    public function verificarTareasVencidas()
    {
        $tareasVencidas = Tarea::where('fecha_vencimiento', '<', now())
            ->where('estado', '!=', 'completada')
            ->whereDoesntHave('asignadoA.notificaciones', function($query) {
                $query->where('tipo', 'tarea_vencida')
                    ->where('created_at', '>', now()->subDay());
            })
            ->get();

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
    }
}