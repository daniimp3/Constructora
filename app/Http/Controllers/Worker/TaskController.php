<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('project')
            ->where('worker_id', Auth::id())
            ->orderBy('deadline', 'asc')
            ->get();
        
        return response()->json($tasks);
    }

    public function myTasks()
    {
        $tasks = Task::with('project')
            ->where('worker_id', Auth::id())
            ->orderBy('status', 'asc')
            ->orderBy('deadline', 'asc')
            ->get();
        
        return response()->json($tasks);
    }

    public function updateStatus(Request $request, Task $task)
    {
        // Verificar que la tarea pertenezca al trabajador
        if ($task->worker_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para actualizar esta tarea'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in-progress,completed'
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de tarea actualizado correctamente',
            'task' => $task->load('project'),
            'project_progress' => $task->project->fresh()->progress
        ]);
    }

    public function stats()
    {
        $workerId = Auth::id();
        
        $totalTasks = Task::where('worker_id', $workerId)->count();
        $completedTasks = Task::where('worker_id', $workerId)->where('status', 'completed')->count();
        $pendingTasks = Task::where('worker_id', $workerId)->where('status', 'pending')->count();
        $inProgressTasks = Task::where('worker_id', $workerId)->where('status', 'in-progress')->count();
        
        return response()->json([
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'in_progress_tasks' => $inProgressTasks
        ]);
    }
}