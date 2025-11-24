<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // Obtener todos los proyectos del supervisor
    public function myProjects()
    {
        $projects = Project::with(['tasks', 'workers', 'expenses', 'evidences', 'materials'])
            ->where('supervisor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($projects);
    }

    // Obtener un proyecto específico con toda su información
    public function show(Project $project)
    {
        // Verificar que el proyecto pertenezca al supervisor
        if ($project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver este proyecto'
            ], 403);
        }

        $project->load([
            'tasks.worker',
            'workers',
            'expenses',
            'evidences',
            'materials',
            'problems.worker'
        ]);

        return response()->json($project);
    }

    // Actualizar el progreso de un proyecto
    public function updateProgress(Request $request, Project $project)
    {
        // Verificar que el proyecto pertenezca al supervisor
        if ($project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para actualizar este proyecto'
            ], 403);
        }

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $project->update(['progress' => $validated['progress']]);

        return response()->json([
            'success' => true,
            'message' => 'Progreso actualizado correctamente',
            'project' => $project
        ]);
    }

    // Obtener estadísticas del supervisor
    public function stats()
    {
        $supervisorId = Auth::id();
        
        $projects = Project::where('supervisor_id', $supervisorId)->get();
        $projectIds = $projects->pluck('id');
        
        // Contar tareas
        $totalTasks = 0;
        $completedTasks = 0;
        
        foreach ($projects as $project) {
            $tasksCount = $project->tasks()->count();
            $completedCount = $project->tasks()->where('status', 'completed')->count();
            
            $totalTasks += $tasksCount;
            $completedTasks += $completedCount;
        }
        
        // Contar problemas no leídos
        $problems = \App\Models\Problem::whereIn('project_id', $projectIds)
            ->where('read', false)
            ->count();
        
        return response()->json([
            'total_projects' => $projects->count(),
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_notifications' => $problems
        ]);
    }

    // Obtener proyectos recientes (para la vista general)
    public function recent()
    {
        $projects = Project::where('supervisor_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();
        
        return response()->json($projects);
    }

    // Verificar si algún proyecto excedió el presupuesto
    public function checkBudgets()
    {
        $projects = Project::where('supervisor_id', Auth::id())
            ->where('status', 'active')
            ->get();
        
        $exceededProjects = $projects->filter(function ($project) {
            return $project->isBudgetExceeded();
        });
        
        $warningProjects = $projects->filter(function ($project) {
            return !$project->isBudgetExceeded() && $project->getBudgetUsedPercentage() >= 90;
        });
        
        return response()->json([
            'exceeded_projects' => $exceededProjects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'budget' => $project->budget,
                    'spent' => $project->spent,
                    'exceeded_amount' => $project->spent - $project->budget,
                    'percentage' => $project->getBudgetUsedPercentage()
                ];
            }),
            'warning_projects' => $warningProjects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'budget' => $project->budget,
                    'spent' => $project->spent,
                    'remaining' => $project->budget - $project->spent,
                    'percentage' => $project->getBudgetUsedPercentage()
                ];
            }),
            'has_exceeded' => $exceededProjects->isNotEmpty(),
            'has_warnings' => $warningProjects->isNotEmpty()
        ]);
    }
}