<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\ExpenseController;

use App\Http\Controllers\Supervisor\AttendanceController;


use App\Http\Controllers\Worker\TaskController as WorkerTaskController;
use App\Http\Controllers\Worker\ProblemController;



// Ruta de prueba (puedes comentarla o eliminarla después)
Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando']);
});


// Ruta para obtener usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


// Rutas protegidas por autenticación
Route::middleware('auth:web')->group(function () {



    Route::get('/attendances', [AttendanceController::class, 'index']);
Route::post('/attendances', [AttendanceController::class, 'store']);

    
    //  RUTAS ADMIN 
    Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/stats', [ProjectController::class, 'getStats']);

        // Proyectos
        Route::get('/projects', [ProjectController::class, 'index']);
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::get('/projects/{project}', [ProjectController::class, 'show']);
        Route::put('/projects/{project}', [ProjectController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
        Route::get('/projects/{project}/stats', [ProjectController::class, 'stats']);
        
        Route::get('/workers/{worker}/temporary-password', [WorkerController::class, 'getTemporaryPassword']);



        // Trabajadores
        Route::get('/workers', [WorkerController::class, 'index']);
        Route::post('/workers', [WorkerController::class, 'store']);
        Route::post('/workers/auto-email', [WorkerController::class, 'storeWithAutoEmail']);
        Route::put('/workers/{worker}', [WorkerController::class, 'update']);
        Route::delete('/workers/{worker}', [WorkerController::class, 'destroy']);
        Route::get('/supervisors', [WorkerController::class, 'supervisors']);
        Route::get('/workers/list', [WorkerController::class, 'workers']);
        
        

        
        // Gastos
        Route::get('/expenses', [ExpenseController::class, 'index']);
        Route::post('/expenses', [ExpenseController::class, 'store']);
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update']);
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);
        Route::get('/projects/{project}/expenses', [ExpenseController::class, 'byProject']);
    

   
        
    


});


   // SUPERVISOR
 Route::prefix('supervisor')->group(function () {
        
        // Estadísticas
        Route::get('/stats', [SupervisorController::class, 'getStats']);
        
        // Proyectos
        Route::get('/my-projects', [SupervisorController::class, 'getMyProjects']);
        Route::get('/projects/recent', [SupervisorController::class, 'getRecentProjects']);
        Route::get('/projects/check-budgets', [SupervisorController::class, 'checkBudgets']);
        Route::get('/projects/{id}', [SupervisorController::class, 'getProjectDetail']);
        Route::put('/projects/{id}/progress', [SupervisorController::class, 'updateProgress']);
        
        // Tareas
        Route::get('/projects/{id}/tasks', [SupervisorController::class, 'getTasks']);
        Route::post('/tasks', [SupervisorController::class, 'storeTask']);
        Route::put('/tasks/{id}', [SupervisorController::class, 'updateTask']);
        Route::delete('/tasks/{id}', [SupervisorController::class, 'deleteTask']);
        
        // Trabajadores
        Route::get('/workers', [SupervisorController::class, 'getWorkers']);
        
        // Evidencias
        Route::get('/projects/{id}/evidences', [SupervisorController::class, 'getEvidences']);
        Route::post('/evidences', [SupervisorController::class, 'storeEvidence']);
        Route::delete('/evidences/{id}', [SupervisorController::class, 'deleteEvidence']);
        
        // Materiales
        Route::get('/projects/{id}/materials', [SupervisorController::class, 'getMaterials']);
        Route::post('/materials', [SupervisorController::class, 'storeMaterial']);
        Route::delete('/materials/{id}', [SupervisorController::class, 'deleteMaterial']);
        
        // Asistencias
        Route::get('/attendance', [SupervisorController::class, 'getAttendance']);
        Route::post('/attendance', [SupervisorController::class, 'storeAttendance']);
        
        // Notificaciones/Problemas
        Route::get('/problems', [SupervisorController::class, 'getProblems']);
        Route::get('/problems/unread', [SupervisorController::class, 'getUnreadProblems']);
        Route::get('/problems/{id}', [SupervisorController::class, 'getProblemDetail']);
        Route::put('/problems/read-all', [SupervisorController::class, 'markAllAsRead']);
        
    });
    



    //  rutas trabajador 
    Route::middleware('role:trabajador')->prefix('worker')->group(function () {
        
        // Tareas
        Route::get('/tasks', [WorkerTaskController::class, 'index']);
        Route::get('/my-tasks', [WorkerTaskController::class, 'myTasks']);
        Route::put('/tasks/{task}/status', [WorkerTaskController::class, 'updateStatus']);
        Route::get('/stats', [WorkerTaskController::class, 'stats']);
        
        // Problemas
        Route::get('/problems', [ProblemController::class, 'index']);
        Route::post('/problems', [ProblemController::class, 'store']);
        Route::get('/my-problems', [ProblemController::class, 'myProblems']);
    });
});