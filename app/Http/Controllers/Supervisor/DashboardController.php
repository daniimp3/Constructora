<?php

namespace App\Http\Controllers\Supervisor;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\Evidence;
use App\Models\Material;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Problem;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // ============== VISTA PRINCIPAL ==============
    
    /**
     * Mostrar el dashboard del supervisor
     */
   public function index()
{
    $supervisor = Auth::user();
    
    // Obtener trabajadores para el JavaScript
    $projects = Project::where('supervisor_id', $supervisor->id)->get();
    
    $workers = [];
    foreach ($projects as $project) {
        $projectWorkers = User::where('role', 'trabajador')
            ->whereHas('projects', function($query) use ($project) {
                $query->where('project_id', $project->id);
            })
            ->get();
        
        foreach ($projectWorkers as $worker) {
            if (!in_array($worker->id, array_column($workers, 'id'))) {
                $workers[] = [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'email' => $worker->email
                ];
            }
        }
    }
    
    return view('supervisor.dashboard', [
        'supervisor' => $supervisor,
        'workers' => $workers  // <-- ESTA LÍNEA FALTABA
    ]);
}
    
    // ============== ESTADÍSTICAS ==============
    
    /**
     * Obtener estadísticas del supervisor
     */
    public function getStats(Request $request)
    {
        try {
            $supervisor = $request->user();
            
            // Contar proyectos del supervisor
            $totalProjects = Project::where('supervisor_id', $supervisor->id)->count();
            
            // Contar tareas totales
            $totalTasks = Task::whereHas('project', function($query) use ($supervisor) {
                $query->where('supervisor_id', $supervisor->id);
            })->count();
            
            // Contar tareas completadas
            $completedTasks = Task::whereHas('project', function($query) use ($supervisor) {
                $query->where('supervisor_id', $supervisor->id);
            })->where('status', 'completed')->count();
            
            // Contar notificaciones pendientes
            $pendingNotifications = Problem::whereHas('project', function($query) use ($supervisor) {
                $query->where('supervisor_id', $supervisor->id);
            })->where('read', false)->count();
            
            return response()->json([
                'total_projects' => $totalProjects,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_notifications' => $pendingNotifications
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ============== PROYECTOS ==============
    
    /**
     * Obtener todos los proyectos del supervisor
     */
    public function getMyProjects(Request $request)
    {
        try {
            $supervisor = $request->user();
            
            $projects = Project::where('supervisor_id', $supervisor->id)
                ->with(['tasks', 'workers'])
                ->get();
            
            return response()->json($projects);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar proyectos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener proyectos recientes del supervisor
     */
    public function getRecentProjects(Request $request)
    {
        try {
            $supervisor = $request->user();
            
            $projects = Project::where('supervisor_id', $supervisor->id)
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();
            
            return response()->json($projects);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar proyectos recientes: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verificar presupuestos de proyectos
     */
    public function checkBudgets(Request $request)
    {
        try {
            $supervisor = $request->user();
            
            $projects = Project::where('supervisor_id', $supervisor->id)->get();
            
            $exceededProjects = [];
            $warningProjects = [];
            
            foreach ($projects as $project) {
                $budget = floatval($project->budget);
                $spent = floatval($project->spent ?? 0);
                
                if ($budget > 0) {
                    $percentage = round(($spent / $budget) * 100, 2);
                    
                    if ($spent > $budget) {
                        $exceededProjects[] = [
                            'id' => $project->id,
                            'name' => $project->name,
                            'budget' => $budget,
                            'spent' => $spent,
                            'exceeded_amount' => $spent - $budget,
                            'percentage' => $percentage
                        ];
                    } elseif ($percentage >= 90) {
                        $warningProjects[] = [
                            'id' => $project->id,
                            'name' => $project->name,
                            'budget' => $budget,
                            'spent' => $spent,
                            'remaining' => $budget - $spent,
                            'percentage' => $percentage
                        ];
                    }
                }
            }
            
            return response()->json([
                'has_exceeded' => count($exceededProjects) > 0,
                'exceeded_projects' => $exceededProjects,
                'has_warnings' => count($warningProjects) > 0,
                'warning_projects' => $warningProjects
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar presupuestos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener detalle de un proyecto
     */
    public function getProjectDetail(Request $request, $id)
    {
        try {
            $supervisor = $request->user();
            
            $project = Project::where('id', $id)
                ->where('supervisor_id', $supervisor->id)
                ->with(['tasks', 'workers'])
                ->firstOrFail();
            
            return response()->json($project);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar proyecto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualizar progreso de un proyecto
     */
    public function updateProgress(Request $request, $id)
    {
        try {
            $supervisor = $request->user();
            
            $validated = $request->validate([
                'progress' => 'required|integer|min:0|max:100'
            ]);
            
            $project = Project::where('id', $id)
                ->where('supervisor_id', $supervisor->id)
                ->firstOrFail();
            
            $project->update(['progress' => $validated['progress']]);
            
            return response()->json([
                'success' => true,
                'message' => 'Progreso actualizado correctamente',
                'project' => $project
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar progreso: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ============== TAREAS ==============
    
    /**
     * Obtener tareas de un proyecto
     */
    public function getTasks(Request $request, $projectId)
    {
        try {
            $supervisor = $request->user();
            
            $project = Project::where('id', $projectId)
                ->where('supervisor_id', $supervisor->id)
                ->firstOrFail();
            
            $tasks = Task::where('project_id', $projectId)
                ->with('worker:id,name')
                ->orderBy('deadline', 'asc')
                ->get();
            
            return response()->json(['tasks' => $tasks]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar tareas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Guardar nueva tarea
     */
    public function storeTask(Request $request)
    {
        try {
            $supervisor = $request->user();
            
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'worker_id' => 'required|exists:users,id',
                'priority' => 'required|in:low,medium,high,urgent',
                'status' => 'required|in:pending,in-progress,completed',
                'deadline' => 'required|date'
            ]);
            
            $project = Project::where('id', $validated['project_id'])
                ->where('supervisor_id', $supervisor->id)
                ->firstOrFail();
            
            $task = Task::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tarea creada correctamente',
                'task' => $task
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear tarea: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualizar tarea existente
     */
    public function updateTask(Request $request, $id)
    {
        try {
            $supervisor = $request->user();
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'worker_id' => 'required|exists:users,id',
                'priority' => 'required|in:low,medium,high,urgent',
                'status' => 'required|in:pending,in-progress,completed',
                'deadline' => 'required|date'
            ]);
            
            $task = Task::findOrFail($id);
            
            $project = Project::where('id', $task->project_id)
                ->where('supervisor_id', $supervisor->id)
                ->firstOrFail();
            
            $task->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tarea actualizada correctamente',
                'task' => $task
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar tarea: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Eliminar tarea
     */
    public function deleteTask(Request $request, $id)
    {
        try {
            $supervisor = $request->user();
            
            $task = Task::findOrFail($id);
            
            $project = Project::where('id', $task->project_id)
                ->where('supervisor_id', $supervisor->id)
                ->firstOrFail();
            
            $task->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Tarea eliminada correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar tarea: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ============== TRABAJADORES ==============
    
    /**
     * Obtener todos los trabajadores asignados al supervisor
     */
    public function getWorkers(Request $request)
{
    try {
        $supervisor = $request->user();
        
        // Opción 1: Traer TODOS los trabajadores del sistema
        $workers = User::where('role', 'trabajador')
            ->select('id', 'name', 'email', 'role')
            ->get()
            ->map(function($worker) use ($supervisor) {
                // Buscar si tiene proyecto asignado con este supervisor
                $task = Task::whereHas('project', function($q) use ($supervisor) {
                    $q->where('supervisor_id', $supervisor->id);
                })
                ->where('worker_id', $worker->id)
                ->with('project')
                ->first();
                
                return [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'email' => $worker->email,
                    'role' => $worker->role,
                    'project_name' => $task && $task->project ? $task->project->name : 'Sin proyecto asignado'
                ];
            })
            ->toArray();
        
        return response()->json($workers);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar trabajadores: ' . $e->getMessage()
        ], 500);
    }
}
    
    // ============== EVIDENCIAS ==============
    
    // Obtener evidencias de un proyecto
    public function getEvidences(Request $request, $projectId)
{
    try {
        $supervisor = $request->user();
        
        // Verificar que el supervisor tiene acceso al proyecto
        $project = Project::where('id', $projectId)
            ->where('supervisor_id', $supervisor->id)
            ->firstOrFail();
        
        $evidences = Evidence::where('project_id', $projectId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Agregar URL completa a las fotos
        $evidences = $evidences->map(function($evidence) {
            if ($evidence->photo_path) {
                $evidence->photo_url = asset('storage/' . $evidence->photo_path); // <-- CAMBIAR ESTA LÍNEA
            }
            return $evidence;
        });
        
        return response()->json($evidences);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar evidencias: ' . $e->getMessage()
        ], 500);
    }
}
    
    /**
     * Guardar nueva evidencia
     */
    public function storeEvidence(Request $request)
    {
        try {
            $supervisor = $request->user();
            
            // Validar datos
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Máximo 5MB
                'date' => 'nullable|date'
            ]);
            
            // Verificar que el supervisor tiene acceso al proyecto
            $project = Project::where('id', $validated['project_id'])
                ->where('supervisor_id', $supervisor->id)
                ->firstOrFail();
            
            // Guardar la foto
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('evidences', 'public');
            }
            
            // Crear evidencia
            $evidence = Evidence::create([
                'project_id' => $validated['project_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'photo_path' => $photoPath,
                'date' => $validated['date'] ?? now(),
                'created_by' => $supervisor->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Evidencia guardada correctamente',
                'evidence' => $evidence
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar evidencia: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
     // Eliminar evidencia
     
    public function deleteEvidence(Request $request, $id)
    {
        try {
            $supervisor = $request->user();
            
            $evidence = Evidence::findOrFail($id);
            
            // Verificar que el supervisor tiene acceso al proyecto
            $project = Project::where('id', $evidence->project_id)
                ->where('supervisor_id', $supervisor->id)
                ->firstOrFail();
            
            // Eliminar foto del storage
            if ($evidence->photo_path) {
                Storage::disk('public')->delete($evidence->photo_path);
            }
            
            // Eliminar evidencia
            $evidence->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Evidencia eliminada correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar evidencia: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ============== MATERIALES ==============
    
    
     // Obtener materiales de un proyecto
     
    public function getMaterials(Request $request, $projectId)
    {
        try {
            $supervisor = $request->user();
            
            // Verificar que el supervisor tiene acceso al proyecto
            $project = Project::where('id', $projectId)
                ->where('supervisor_id', $supervisor->id)
                ->firstOrFail();
            
            $materials = Material::where('project_id', $projectId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json($materials);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar materiales: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
     // Guardar nuevo material
     
    public function storeMaterial(Request $request)
    {
        try {
            $supervisor = $request->user();
            
            // Validar datos
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'name' => 'required|string|max:255',
                'quantity' => 'required|numeric|min:0.01',
                'unit' => 'required|string|max:50',
                'cost' => 'required|numeric|min:0'
            ]);
            
            // Verificar que el supervisor tiene acceso al proyecto
            $project = Project::where('id', $validated['project_id'])
                ->where('supervisor_id', $supervisor->id)
                ->firstOrFail();
            
            // Crear material
            $material = Material::create([
                'project_id' => $validated['project_id'],
                'name' => $validated['name'],
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'cost' => $validated['cost'],
                'created_by' => $supervisor->id
            ]);
            
            // Actualizar el gasto total del proyecto
            $this->updateProjectSpent($project);
            
            return response()->json([
                'success' => true,
                'message' => 'Material guardado correctamente',
                'material' => $material
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar material: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
     // Eliminar material
     
    public function deleteMaterial(Request $request, $id)
    {
        try {
            $supervisor = $request->user();
            
            $material = Material::findOrFail($id);
            
            // Verificar que el supervisor tiene acceso al proyecto
            $project = Project::where('id', $material->project_id)
                ->where('supervisor_id', $supervisor->id)
                ->firstOrFail();
            
            // Eliminar material
            $material->delete();
            
            // Actualizar el gasto total del proyecto
            $this->updateProjectSpent($project);
            
            return response()->json([
                'success' => true,
                'message' => 'Material eliminado correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar material: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
     // Actualizar el gasto total del proyecto
     
    private function updateProjectSpent(Project $project)
    {
        $totalSpent = Material::where('project_id', $project->id)
            ->selectRaw('SUM(quantity * cost) as total')
            ->value('total') ?? 0;
        
        $project->update(['spent' => $totalSpent]);
    }
    
    // ============== ASISTENCIAS ==============
    
    //
     // Obtener asistencias de una fecha
     
    public function getAttendance(Request $request)
{
    try {
        $supervisor = $request->user();
        $date = $request->input('date', now()->format('Y-m-d'));
        
        // Obtener todas las asistencias de esa fecha
        $attendances = Attendance::where('date', $date)
            ->with(['worker:id,name'])
            ->get();
        
        return response()->json($attendances);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar asistencias: ' . $e->getMessage()
        ], 500);
    }
}
    
    
     // Guardar/actualizar asistencia
     
    public function storeAttendance(Request $request)
{
    try {
        $supervisor = $request->user();
        
        // Validar datos
        $validated = $request->validate([
            'worker_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late',
            'time' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:500'
        ]);
        
        // Buscar si ya existe un registro de asistencia
        $attendance = Attendance::where('worker_id', $validated['worker_id'])
            ->where('date', $validated['date'])
            ->first();
        
        if ($attendance) {
            // Actualizar existente
            $attendance->update([
                'status' => $validated['status'],
                'time' => $validated['time'] ?? null,
                'notes' => $validated['notes'] ?? null
            ]);
            
            $message = 'Asistencia actualizada correctamente';
            
        } else {
            // Crear nueva
            $attendance = Attendance::create([
                'worker_id' => $validated['worker_id'],
                'date' => $validated['date'],
                'status' => $validated['status'],
                'time' => $validated['time'] ?? null,
                'notes' => $validated['notes'] ?? null
            ]);
            
            $message = 'Asistencia guardada correctamente';
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'attendance' => $attendance
        ], 201);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Datos inválidos',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar asistencia: ' . $e->getMessage()
        ], 500);
    }
}
    
    // ============== NOTIFICACIONES/PROBLEMAS ==============
    
    
     // Obtener problemas/notificaciones
     
    public function getProblems(Request $request)
    {
        try {
            $supervisor = $request->user();
            
            $problems = Problem::whereHas('project', function($query) use ($supervisor) {
                $query->where('supervisor_id', $supervisor->id);
            })
            ->with(['worker:id,name', 'project:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();
            
            return response()->json($problems);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
     // Obtener conteo de problemas no leídos
     
    public function getUnreadProblems(Request $request)
    {
        try {
            $supervisor = $request->user();
            
            $count = Problem::whereHas('project', function($query) use ($supervisor) {
                $query->where('supervisor_id', $supervisor->id);
            })
            ->where('read', false)
            ->count();
            
            return response()->json(['count' => $count]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
      //Obtener detalle de un problema
     
    public function getProblemDetail(Request $request, $id)
    {
        try {
            $supervisor = $request->user();
            
            $problem = Problem::where('id', $id)
                ->whereHas('project', function($query) use ($supervisor) {
                    $query->where('supervisor_id', $supervisor->id);
                })
                ->with(['worker:id,name', 'project:id,name'])
                ->firstOrFail();
            
            // Marcar como leído
            $problem->update(['read' => true]);
            
            return response()->json($problem);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar detalle: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
     // Marcar todos los problemas como leídos
     
    public function markAllAsRead(Request $request)
    {
        try {
            $supervisor = $request->user();
            
            Problem::whereHas('project', function($query) use ($supervisor) {
                $query->where('supervisor_id', $supervisor->id);
            })
            ->where('read', false)
            ->update(['read' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Todas las notificaciones marcadas como leídas'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}