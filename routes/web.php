<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Ruta de prueba
Route::get('/test', function () {
    return 'Funciona correctamente';
});

// Ruta principal
Route::get('/', function () {
    return redirect('/login');
});

// Mostrar login
Route::get('/login', function () {
    return view('login');
})->name('login');

// Procesar login
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        $user = Auth::user();
        
        if (str_contains($user->email, 'admin')) {
            return response()->json([
                'success' => true,
                'redirect' => '/admin/dashboard'
            ]);
        } elseif (str_contains($user->email, 'supervisor')) {
            return response()->json([
                'success' => true,
                'redirect' => '/supervisor/dashboard'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'redirect' => '/trabajador/dashboard'
            ]);
        }
    }

    return response()->json([
        'success' => false,
        'message' => 'Credenciales incorrectas'
    ], 401);
});

// Cerrar sesión
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Dashboards SIN autenticación (temporal para desarrollo)
Route::get('/admin/dashboard', function () {
    return view('admin');
})->name('admin.dashboard');

Route::get('/supervisor/dashboard', function () {
    return view('supervisor');
})->name('supervisor.dashboard');

Route::get('/trabajador/dashboard', function () {
    return view('trabajador');
})->name('trabajador.dashboard');

// Redirecciones cortas
Route::get('/admin', function () {
    return redirect('/admin/dashboard');
});

Route::get('/supervisor', function () {
    return redirect('/supervisor/dashboard');
});

Route::get('/trabajador', function () {
    return redirect('/trabajador/dashboard');
});