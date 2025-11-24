<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\WorkerController;

use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboard;


use App\Http\Controllers\Supervisor\ProjectController as SupervisorProjectController;
use App\Http\Controllers\Supervisor\ProblemController as SupervisorProblemController;

use App\Http\Controllers\Worker\DashboardController as WorkerDashboard;

use App\Http\Controllers\ExportController;

Route::get('/projects/{project}/export/pdf', [ExportController::class, 'exportPDF']);
Route::get('/projects/{project}/export/excel', [ExportController::class, 'exportExcel']);



// Redirigir raíz a login
Route::get('/', function () {
    return redirect('/login');
});

//  RUTAS DE AUTENTICACIÓN 
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//  RUTAS DEL ADMIN 
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    
    // Proyectos
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    
    // Trabajadores
    Route::post('/workers', [WorkerController::class, 'store'])->name('workers.store');
    Route::put('/workers/{worker}', [WorkerController::class, 'update'])->name('workers.update');
    Route::delete('/workers/{worker}', [WorkerController::class, 'destroy'])->name('workers.destroy');
});

Route::middleware(['auth', 'role:supervisor'])
    ->prefix('supervisor')
    ->name('supervisor.')
    ->group(function () {
        // Dashboard principal
        Route::get('/dashboard', [SupervisorDashboard::class, 'index'])->name('dashboard');
    });



Route::middleware(['auth', 'role:supervisor'])->group(function () {
    // VISTA PRINCIPAL (esta va sin /api)
    Route::get('/supervisor/dashboard', [SupervisorDashboard::class, 'index'])->name('supervisor.dashboard');
});

// Rutas API del supervisor (con prefijo /api/supervisor)
Route::middleware(['auth', 'role:supervisor'])
    ->prefix('api/supervisor')
    ->group(function () {
        // ESTADÍSTICAS
        Route::get('/stats', [SupervisorDashboard::class, 'getStats']);

        // PROYECTOS
        Route::get('/my-projects', [SupervisorDashboard::class, 'getMyProjects']);
        Route::get('/projects/recent', [SupervisorDashboard::class, 'getRecentProjects']);
        Route::get('/projects/check-budgets', [SupervisorDashboard::class, 'checkBudgets']);
        Route::get('/projects/{id}', [SupervisorDashboard::class, 'getProjectDetail']);
        Route::put('/projects/{id}/progress', [SupervisorDashboard::class, 'updateProgress']);

        // TAREAS
        Route::get('/projects/{id}/tasks', [SupervisorDashboard::class, 'getTasks']);
        Route::post('/tasks', [SupervisorDashboard::class, 'storeTask']);
        Route::put('/tasks/{id}', [SupervisorDashboard::class, 'updateTask']);
        Route::delete('/tasks/{id}', [SupervisorDashboard::class, 'deleteTask']);

        // TRABAJADORES
        Route::get('/workers', [SupervisorDashboard::class, 'getWorkers']);

        // EVIDENCIAS
        Route::get('/projects/{id}/evidences', [SupervisorDashboard::class, 'getEvidences']);
        Route::post('/evidences', [SupervisorDashboard::class, 'storeEvidence']);
        Route::delete('/evidences/{id}', [SupervisorDashboard::class, 'deleteEvidence']);

        // MATERIALES
        Route::get('/projects/{id}/materials', [SupervisorDashboard::class, 'getMaterials']);
        Route::post('/materials', [SupervisorDashboard::class, 'storeMaterial']);
        Route::delete('/materials/{id}', [SupervisorDashboard::class, 'deleteMaterial']);

        // ASISTENCIAS
        Route::get('/attendance', [SupervisorDashboard::class, 'getAttendance']);
        Route::post('/attendance', [SupervisorDashboard::class, 'storeAttendance']);

        // NOTIFICACIONES
        Route::get('/problems', [SupervisorDashboard::class, 'getProblems']);
        Route::get('/problems/unread', [SupervisorDashboard::class, 'getUnreadProblems']);
        Route::get('/problems/{id}', [SupervisorDashboard::class, 'getProblemDetail']);
        Route::put('/problems/read-all', [SupervisorDashboard::class, 'markAllAsRead']);
    });

//  RUTAS DEL TRABAJADOR
Route::middleware(['auth', 'role:trabajador'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [WorkerDashboard::class, 'index'])->name('dashboard');
});