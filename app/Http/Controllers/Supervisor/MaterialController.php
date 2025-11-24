<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with('project');

        // Filtrar por proyectos del supervisor
        if ($request->has('my_projects')) {
            $projectIds = Project::where('supervisor_id', Auth::id())->pluck('id');
            $query->whereIn('project_id', $projectIds);
        }

        // Filtrar por proyecto específico
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $materials = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($materials);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        // Verificar que el supervisor tenga acceso al proyecto
        $project = Project::findOrFail($validated['project_id']);
        if ($project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para registrar materiales en este proyecto'
            ], 403);
        }

        $material = Material::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Material registrado correctamente',
            'material' => $material->load('project')
        ]);
    }

    public function destroy(Material $material)
    {
        // Verificar que el supervisor tenga acceso al proyecto
        if ($material->project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este material'
            ], 403);
        }

        $material->delete();

        return response()->json([
            'success' => true,
            'message' => 'Material eliminado correctamente'
        ]);
    }

    // Obtener materiales de un proyecto específico
    public function byProject(Project $project)
    {
        // Verificar que el supervisor tenga acceso al proyecto
        if ($project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver los materiales de este proyecto'
            ], 403);
        }

        $materials = $project->materials()->orderBy('created_at', 'desc')->get();
        
        $totalCost = $materials->sum(function ($material) {
            return $material->total_cost;
        });
        
        return response()->json([
            'materials' => $materials,
            'total_cost' => $totalCost
        ]);
    }
}