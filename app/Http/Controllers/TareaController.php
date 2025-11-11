<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TareaController extends Controller
{
    // CEO-HU-02: Asignar tareas a trabajadores
    // CEO-HU-06: Consultar tareas asignadas
    public function index(Request $request, Proyecto $proyecto)
    {
        $query = $proyecto->tareas()->with(['asignadoA', 'creadoPor']);

        // Filtro por usuario (para trabajadores)
        if ($request->has('mis_tareas') || Auth::user()->rol === 'trabajador') {
            $query->where('asignado_a', Auth::id());
        }

        // Filtros adicionales
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        $tareas = $query->orderBy('fecha_vencimiento', 'asc')->paginate(15);

        return view('tareas.index', compact('proyecto', 'tareas'));
    }

    public function store(Request $request, Proyecto $proyecto)
    {
        $this->authorize('update', $proyecto);

        $validado = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'fecha_inicio' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date|after:fecha_inicio',
            'asignado_a' => 'required|exists:usuarios,id',
        ]);

        $validado['creado_por'] = Auth::id();

        $tarea = $proyecto->tareas()->create($validado);

        // Notificar al trabajador asignado
        $this->notificarAsignacionTarea($tarea);

        return redirect()
            ->route('tareas.index', $proyecto)
            ->with('success', 'Tarea asignada exitosamente');
    }

    // CEO-HU-09: Marcar tareas como completadas
    public function completar(Proyecto $proyecto, $idTarea)
    {
        $tarea = $proyecto->tareas()->findOrFail($idTarea);
        
        $this->authorize('complete', $tarea);

        $tarea->update([
            'estado' => 'completada',
            'fecha_completada' => now(),
            'porcentaje_completado' => 100,
        ]);

        // Actualizar porcentaje de avance del proyecto
        $proyecto->actualizarPorcentajeAvance();

        return redirect()
            ->route('tareas.index', $proyecto)
            ->with('success', 'Tarea marcada como completada');
    }

    private function notificarAsignacionTarea($tarea)
    {
        Notificacion::create([
            'id_usuario' => $tarea->asignado_a,
            'tipo' => 'tarea_asignada',
            'titulo' => 'Nueva tarea asignada',
            'mensaje' => "Se te ha asignado la tarea: {$tarea->nombre}",
            'datos' => [
                'id_tarea' => $tarea->id,
                'id_proyecto' => $tarea->id_proyecto,
            ],
        ]);
    }
}