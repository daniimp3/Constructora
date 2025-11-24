<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProblemController extends Controller
{
    // Obtener todos los problemas de los proyectos del supervisor
    public function index()
    {
        $problems = Problem::with(['project', 'worker'])
            ->forSupervisor(Auth::id())
            ->orderBy('read', 'asc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($problems);
    }

    // Obtener problemas no leídos
    public function unread()
    {
        $problems = Problem::with(['project', 'worker'])
            ->forSupervisor(Auth::id())
            ->unread()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'count' => $problems->count(),
            'problems' => $problems
        ]);
    }

    // Ver detalle de un problema
    public function show(Problem $problem)
    {
        // Verificar que el problema pertenezca a un proyecto del supervisor
        if ($problem->project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver este problema'
            ], 403);
        }

        // Marcar como leído automáticamente
        if (!$problem->read) {
            $problem->markAsRead();
        }

        return response()->json($problem->load(['project', 'worker']));
    }

    // Marcar un problema como leído
    public function markAsRead(Problem $problem)
    {
        // Verificar que el problema pertenezca a un proyecto del supervisor
        if ($problem->project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para marcar este problema'
            ], 403);
        }

        $problem->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Problema marcado como leído',
            'problem' => $problem
        ]);
    }

    // Marcar todos los problemas como leídos
    public function markAllAsRead()
    {
        $projectIds = Project::where('supervisor_id', Auth::id())->pluck('id');
        
        Problem::whereIn('project_id', $projectIds)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Todos los problemas marcados como leídos'
        ]);
    }

    // Obtener problemas por proyecto
    public function byProject(Project $project)
    {
        // Verificar que el proyecto pertenezca al supervisor
        if ($project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver los problemas de este proyecto'
            ], 403);
        }

        $problems = $project->problems()
            ->with('worker')
            ->orderBy('read', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'problems' => $problems,
            'total' => $problems->count(),
            'unread' => $problems->where('read', false)->count()
        ]);
    }

    // Obtener estadísticas de problemas
    public function stats()
    {
        $projectIds = Project::where('supervisor_id', Auth::id())->pluck('id');
        
        $totalProblems = Problem::whereIn('project_id', $projectIds)->count();
        $unreadProblems = Problem::whereIn('project_id', $projectIds)->unread()->count();
        
        $problemsByPriority = Problem::whereIn('project_id', $projectIds)
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority');
        
        $problemsByCategory = Problem::whereIn('project_id', $projectIds)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->pluck('count', 'category');
        
        return response()->json([
            'total_problems' => $totalProblems,
            'unread_problems' => $unreadProblems,
            'by_priority' => [
                'urgent' => $problemsByPriority['urgent'] ?? 0,
                'high' => $problemsByPriority['high'] ?? 0,
                'medium' => $problemsByPriority['medium'] ?? 0,
                'low' => $problemsByPriority['low'] ?? 0
            ],
            'by_category' => $problemsByCategory
        ]);
    }
}