<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\User;
class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('supervisor')->get();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|in:active,paused,completed',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:0',
            'supervisor_id' => 'nullable|exists:users,id'
        ]);

        $validated['progress'] = 0;
        $validated['spent'] = 0;

        $project = Project::create($validated);
        return response()->json($project->load('supervisor'), 201);
    }

    public function getStats()
{
    $stats = [
        'total_projects' => Project::count(),
        'active_projects' => Project::where('status', 'active')->count(),
        'total_budget' => Project::sum('budget'),
        'total_workers' => User::where('role', '!=', 'admin')->count(),
    ];
    
    return response()->json($stats);
}

    public function show(Project $project)
    {
        return response()->json($project->load(['supervisor', 'workers', 'tasks', 'evidences', 'materials']));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|in:active,paused,completed',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:0',
            'supervisor_id' => 'nullable|exists:users,id'
        ]);

        $project->update($validated);
        return response()->json($project->load('supervisor'));
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Proyecto eliminado correctamente']);
    }
}