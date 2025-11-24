<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProblemController extends Controller
{
    public function index()
    {
        $problems = Problem::with('project')
            ->where('worker_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($problems);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'location' => 'nullable|string|max:255'
        ]);

        $validated['worker_id'] = Auth::id();
        $validated['read'] = false;

        $problem = Problem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Problema reportado correctamente',
            'problem' => $problem->load('project')
        ]);
    }

    public function myProblems()
    {
        $problems = Problem::with('project')
            ->where('worker_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json($problems);
    }
}