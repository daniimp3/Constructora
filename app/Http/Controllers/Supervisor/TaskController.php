<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    // Obtener proyectos del supervisor
    public function myProjects()
    {
        $projects = Project::with(['tasks', 'workers', 'expenses'])
            ->where('supervisor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($projects);
    }

    // Obtener estadísticas del supervisor
    public function stats()
    {
        $supervisorId = Auth::id();
        
        $projects = Project::where('supervisor_id', $supervisorId)->get();
        
        $totalProjects = $projects->count();
        $totalTasks = Task::whereIn('project_id', $projects->pluck('id'))->count();
        $completedTasks = Task::whereIn('project_id', $projects->pluck('id'))
            ->where('status', 'completed')
            ->count();
        
        $problems = \App\Models\Problem::whereIn('project_id', $projects->pluck('id'))
            ->unread()
            ->count();
        
        return response()->json([
            'total_projects' => $totalProjects,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_notifications' => $problems
        ]);
    }
}

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['project', 'worker']);

        // Filtrar por proyectos del supervisor
        if ($request->has('my_projects')) {
            $projectIds = Project::where('supervisor_id', Auth::id())->pluck('id');
            $query->whereIn('project_id', $projectIds);
        }

        // Filtrar por proyecto específico
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->orderBy('deadline', 'asc')->get();
        
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'worker_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,in-progress,completed',
            'priority' => 'required|in:low,medium,high,urgent',
            'deadline' => 'required|date'
        ]);

        // Verificar que el supervisor tenga acceso al proyecto
        $project = Project::findOrFail($validated['project_id']);
        if ($project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para crear tareas en este proyecto'
            ], 403);
        }

        $task = Task::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarea creada correctamente',
            'task' => $task->load(['project', 'worker']),
            'project_progress' => $project->fresh()->progress
        ]);
    }

    public function update(Request $request, Task $task)
    {
        // Verificar que el supervisor tenga acceso al proyecto
        if ($task->project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para editar esta tarea'
            ], 403);
        }

        $validated = $request->validate([
            'worker_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,in-progress,completed',
            'priority' => 'required|in:low,medium,high,urgent',
            'deadline' => 'required|date'
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarea actualizada correctamente',
            'task' => $task->load(['project', 'worker']),
            'project_progress' => $task->project->fresh()->progress
        ]);
    }

    public function destroy(Task $task)
    {
        // Verificar que el supervisor tenga acceso al proyecto
        if ($task->project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta tarea'
            ], 403);
        }

        $projectId = $task->project_id;
        $task->delete();

        $project = Project::find($projectId);

        return response()->json([
            'success' => true,
            'message' => 'Tarea eliminada correctamente',
            'project_progress' => $project->fresh()->progress
        ]);
    }

    // Obtener tareas de un proyecto específico
    public function byProject(Project $project)
    {
        // Verificar que el supervisor tenga acceso al proyecto
        if ($project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver las tareas de este proyecto'
            ], 403);
        }

        $tasks = $project->tasks()->with('worker')->orderBy('deadline', 'asc')->get();
        
        return response()->json([
            'tasks' => $tasks,
            'project_progress' => $project->progress
        ]);
    }
}