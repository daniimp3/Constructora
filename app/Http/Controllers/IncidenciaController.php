<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IncidenciaController extends Controller
{
    // CEO-HU-11: Notificar problemas en el proyecto
    public function store(Request $request, Proyecto $proyecto)
    {
        $validado = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'severidad' => 'required|in:baja,media,alta,critica',
        ]);

        $validado['reportado_por'] = Auth::id();
        $validado['estado'] = 'reportado';

        $incidencia = $proyecto->incidencias()->create($validado);

        // Notificar al administrador
        $this->notificarAdministrador($incidencia, $proyecto);

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('success', 'Incidencia reportada exitosamente');
    }

    public function index(Proyecto $proyecto)
    {
        $incidencias = $proyecto->incidencias()
            ->with(['reportadoPor', 'asignadoA'])
            ->orderBy('severidad', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('incidencias.index', compact('proyecto', 'incidencias'));
    }

    private function notificarAdministrador($incidencia, $proyecto)
    {
        Notificacion::create([
            'id_usuario' => $proyecto->id_administrador,
            'tipo' => 'incidencia_reportada',
            'titulo' => 'Nueva incidencia reportada',
            'mensaje' => "Incidencia '{$incidencia->titulo}' - Severidad: {$incidencia->severidad}",
            'datos' => [
                'id_incidencia' => $incidencia->id,
                'id_proyecto' => $proyecto->id,
            ],
        ]);
    }
}
