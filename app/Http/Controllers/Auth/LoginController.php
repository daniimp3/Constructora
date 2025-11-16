<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redireccionar según el rol
            switch ($user->role) {
                case 'admin':
                    return response()->json([
                        'success' => true,
                        'redirect' => route('admin.dashboard')
                    ]);
                case 'supervisor':
                    return response()->json([
                        'success' => true,
                        'redirect' => route('supervisor.dashboard')
                    ]);
                case 'trabajador':
                    return response()->json([
                        'success' => true,
                        'redirect' => route('worker.dashboard')
                    ]);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Rol de usuario no válido'
                    ], 403);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Las credenciales proporcionadas son incorrectas.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}