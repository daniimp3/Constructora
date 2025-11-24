<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * mostrar el formulario de inicio de sesión.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * manejar una solicitud de inicio de sesión a la aplicación.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // validar los datos del formulario
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // obtener las credenciales del request
        $credentials = $request->only('email', 'password');

        // intentar autenticar al usuario
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // regenerar la sesión para prevenir ataques de fijación de sesión
            $request->session()->regenerate();

            // obtener el usuario autenticado
            $user = Auth::user();

            // redirigir según el rol del usuario
            return $this->redirectBasedOnRole($user);
        }

        // si la autenticación falla, redirigir de vuelta con error
        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * redirigir al usuario según su rol.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin':
                // redirigir al dashboard de administrador
                return redirect()->intended('/admin/dashboard');
            
            case 'supervisor':
                // redirigir al dashboard de supervisor
                return redirect()->intended('/supervisor/dashboard');
            
            case 'trabajador':
                // redirigir al dashboard de trabajador
                return redirect()->intended('/worker/dashboard');
            
            default:
                // si el rol no es válido, cerrar sesión y mostrar error
                Auth::logout();
                return redirect('/')->withErrors([
                    'email' => 'Rol de usuario no válido.',
                ]);
        }
    }

    /**
     * cerrar sesión del usuario de la aplicación.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // cerrar la sesión del usuario
        Auth::logout();

        // invalidar la sesión actual
        $request->session()->invalidate();

        // regenerar el token CSRF
        $request->session()->regenerateToken();

        // redirigir a la página de inicio
        return redirect('/');
    }
}