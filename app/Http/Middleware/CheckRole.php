<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect('/')->withErrors(['error' => 'Debes iniciar sesión.']);
        }

        // Verificar si el usuario tiene el rol correcto
        if (auth()->user()->role !== $role) {
            // Redirigir según el rol que tiene
            return match(auth()->user()->role) {
                'admin' => redirect('/admin/dashboard')->withErrors(['error' => 'No tienes permiso para acceder a esa página.']),
                'supervisor' => redirect('/supervisor/dashboard')->withErrors(['error' => 'No tienes permiso para acceder a esa página.']),
                'trabajador' => redirect('/worker/dashboard')->withErrors(['error' => 'No tienes permiso para acceder a esa página.']),
                default => redirect('/')->withErrors(['error' => 'Rol no válido.']),
            };
        }

        return $next($request);
    }
}