<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Evidence;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Evidence::with('project');

        // Filtrar por proyectos del supervisor
        if ($request->has('my_projects')) {
            $projectIds = Project::where('supervisor_id', Auth::id())->pluck('id');
            $query->whereIn('project_id', $projectIds);
        }

        // Filtrar por proyecto específico
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $evidences = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($evidences);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB max
        ]);

        // Verificar que el supervisor tenga acceso al proyecto
        $project = Project::findOrFail($validated['project_id']);
        if ($project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para subir evidencias a este proyecto'
            ], 403);
        }

        // Guardar la imagen
        $photoPath = $request->file('photo')->store('evidences', 'public');

        $evidence = Evidence::create([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'photo_path' => $photoPath
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Evidencia subida correctamente',
            'evidence' => $evidence->load('project')
        ]);
    }

    public function destroy(Evidence $evidence)
    {
        // Verificar que el supervisor tenga acceso al proyecto
        if ($evidence->project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta evidencia'
            ], 403);
        }

        $evidence->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evidencia eliminada correctamente'
        ]);
    }

    // Obtener evidencias de un proyecto específico
    public function byProject(Project $project)
    {
        // Verificar que el supervisor tenga acceso al proyecto
        if ($project->supervisor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver las evidencias de este proyecto'
            ], 403);
        }

        $evidences = $project->evidences()->orderBy('created_at', 'desc')->get();
        
        return response()->json($evidences);
    }
}