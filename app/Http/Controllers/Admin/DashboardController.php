<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // cargar todos los datos que necesita el dashboard
        $workers = User::with('project')
            ->where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $projects = Project::with(['supervisor', 'workers', 'tasks'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'total_budget' => Project::sum('budget'),
            'total_workers' => User::where('role', '!=', 'admin')->count(),
        ];
        
        return view('admin.dashboard', compact('workers', 'projects', 'stats'));
    }
}