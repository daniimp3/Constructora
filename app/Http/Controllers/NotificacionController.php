<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CONTROLADOR DE NOTIFICACIONES
 * 
 * Maneja todas las operaciones relacionadas con las notificaciones del usuario
 */
class NotificacionController extends Controller
{
    /**
     * Mostrar todas las notificaciones del usuario
     */
    public function index()
    {
        // Obtener notificaciones del usuario autenticado
        $notificaciones = Notificacion::where('id_usuario', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notificaciones.index', compact('notificaciones'));
    }

    /**
     * Marcar una notificación como leída
     */
    public function marcarComoLeida(Notificacion $notificacion)
    {
        // Verificar que el usuario puede actualizar esta notificación
        $this->authorize('update', $notificacion);
        
        // Marcar como leída
        $notificacion->marcarComoLeida();

        return back()->with('success', 'Notificación marcada como leída');
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasComoLeidas()
    {
        // Actualizar todas las notificaciones no leídas del usuario
        Notificacion::where('id_usuario', Auth::id())
            ->where('fue_leida', false)
            ->update([
                'fue_leida' => true,
                'leida_en' => now(),
            ]);

        return back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }
}
