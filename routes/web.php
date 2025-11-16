<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\WorkerController as AdminWorkerController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\ProjectController as SupervisorProjectController;
use App\Http\Controllers\Supervisor\TaskController;
use App\Http\Controllers\Supervisor\EvidenceController;
use App\Http\Controllers\Supervisor\MaterialController;
use App\Http\Controllers\Supervisor\ProblemController;
use App\Http\Controllers\Supervisor\AttendanceController;
use App\Http\Controllers\Worker\DashboardController as WorkerDashboardController;
use App\Http\Controllers\Worker\TaskController as WorkerTaskController;
use App\Http\Controllers\Worker\ProblemController as WorkerProblemController;


/*RUTAS PÚBLICAS */

// Página de inicio
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/* AUTENTICACIÓN */

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


/* REDIRECCIÓN AUTOMÁTICA SEGÚN ROL*/

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'supervisor':
                return redirect()->route('supervisor.dashboard');
            case 'trabajador':
                return redirect()->route('worker.dashboard');
            default:
                abort(403, 'Rol no autorizado');
        }
    })->name('dashboard');
});

/*
 RUTAS DE ADMINISTRADOR
 Acceso: Solo usuarios con rol 'admin'
 Prefijo: /admin
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // API para obtener datos
    Route::get('/api/projects', [AdminDashboardController::class, 'getProjects'])->name('api.projects');
    Route::get('/api/workers', [AdminDashboardController::class, 'getWorkers'])->name('api.workers');
    
    // CRUD de Proyectos
    Route::post('/projects', [AdminProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [AdminProjectController::class, 'show'])->name('projects.show');
    Route::put('/projects/{project}', [AdminProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [AdminProjectController::class, 'destroy'])->name('projects.destroy');
    
    // CRUD de Trabajadores
    Route::post('/workers', [AdminWorkerController::class, 'store'])->name('workers.store');
    Route::get('/workers/{worker}', [AdminWorkerController::class, 'show'])->name('workers.show');
    Route::put('/workers/{worker}', [AdminWorkerController::class, 'update'])->name('workers.update');
    Route::delete('/workers/{worker}', [AdminWorkerController::class, 'destroy'])->name('workers.destroy');
});

/*
 RUTAS DE SUPERVISOR
 Acceso: Solo usuarios con rol 'supervisor'
 Prefijo: /supervisor
*/

Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
    
    // API para obtener datos
    Route::get('/api/projects', [SupervisorDashboardController::class, 'getProjects'])->name('api.projects');
    Route::get('/api/problems', [ProblemController::class, 'index'])->name('api.problems');
    Route::get('/api/attendances', [AttendanceController::class, 'index'])->name('api.attendances');
    
    // Actualizar avance de proyecto
    Route::patch('/projects/{project}/progress', [SupervisorProjectController::class, 'updateProgress'])->name('projects.progress');
    
    // Gestión de Tareas
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    
    // Gestión de Evidencias
    Route::post('/projects/{project}/evidences', [EvidenceController::class, 'store'])->name('evidences.store');
    Route::delete('/evidences/{evidence}', [EvidenceController::class, 'destroy'])->name('evidences.destroy');
    
    // Gestión de Materiales
    Route::post('/projects/{project}/materials', [MaterialController::class, 'store'])->name('materials.store');
    Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');
    
    // Gestión de Problemas/Notificaciones
    Route::patch('/problems/{problem}/read', [ProblemController::class, 'markAsRead'])->name('problems.read');
    Route::post('/problems/read-all', [ProblemController::class, 'markAllAsRead'])->name('problems.readAll');
    
    // Gestión de Asistencias
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
});

/*
 RUTAS DE TRABAJADOR
 Acceso: Solo usuarios con rol 'trabajador'
 Prefijo: /worker
*/

Route::middleware(['auth', 'role:trabajador'])->prefix('worker')->name('worker.')->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', [WorkerDashboardController::class, 'index'])->name('dashboard');
    
    // API para obtener datos
    Route::get('/api/projects', [WorkerDashboardController::class, 'getProjects'])->name('api.projects');
    Route::get('/api/tasks', [WorkerDashboardController::class, 'getTasks'])->name('api.tasks');
    Route::get('/api/problems', [WorkerProblemController::class, 'index'])->name('api.problems');
    
    // Actualizar estado de tareas
    Route::patch('/tasks/{task}/status', [WorkerTaskController::class, 'updateStatus'])->name('tasks.status');
    
    // Reportar problemas
    Route::post('/problems', [WorkerProblemController::class, 'store'])->name('problems.store');
});