<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    // CEO-HU-13: Registrar asistencia del personal
    public function store(Request $request, Proyecto $proyecto)
    {
        $this->authorize('update', $proyecto);

        $validado = $request->validate([
            'id_usuario' => 'required|exists:usuarios,id',
            'fecha_asistencia' => 'required|date',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i|after:hora_entrada',
            'estado' => 'required|in:presente,ausente,tardanza,permiso',
            'notas' => 'nullable|string',
        ]);

        $validado['registrado_por'] = Auth::id();

        $proyecto->asistencias()->create($validado);

        return redirect()
            ->back()
            ->with('success', 'Asistencia registrada exitosamente');
    }

    public function index(Request $request, Proyecto $proyecto)
    {
        $fecha = $request->input('fecha', now()->toDateString());

        $asistencias = $proyecto->asistencias()
            ->with('usuario')
            ->whereDate('fecha_asistencia', $fecha)
            ->get();

        $trabajadores = Usuario::where('rol', 'trabajador')->get();

        return view('asistencias.index', compact('proyecto', 'asistencias', 'trabajadores', 'fecha'));
    }
}