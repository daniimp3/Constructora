<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AvanceObraController extends Controller
{
    // CEO-HU-04: Subir avances de obra con evidencias
    public function store(Request $request, Proyecto $proyecto)
    {
        $this->authorize('update', $proyecto);

        $validado = $request->validate([
            'id_tarea' => 'nullable|exists:tareas,id',
            'fecha_avance' => 'required|date',
            'descripcion' => 'required|string',
            'notas' => 'nullable|string',
            'porcentaje_avance' => 'required|numeric|min:0|max:100',
            'fotos.*' => 'nullable|image|max:5120',
            'documentos.*' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $validado['id_supervisor'] = Auth::id();

        DB::transaction(function() use ($validado, $request, $proyecto) {
            $avance = $proyecto->avancesObra()->create($validado);

            // Subir fotos
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $ruta = $foto->store('avances/' . $proyecto->id, 'public');
                    $avance->multimedia()->create([
                        'tipo' => 'foto',
                        'ruta_archivo' => $ruta,
                        'nombre_archivo' => $foto->getClientOriginalName(),
                    ]);
                }
            }

            // Subir documentos
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $doc) {
                    $ruta = $doc->store('avances/' . $proyecto->id, 'public');
                    $avance->multimedia()->create([
                        'tipo' => 'documento',
                        'ruta_archivo' => $ruta,
                        'nombre_archivo' => $doc->getClientOriginalName(),
                    ]);
                }
            }

            // Actualizar porcentaje de tarea si está asociada
            if ($validado['id_tarea']) {
                $tarea = Tarea::find($validado['id_tarea']);
                $tarea->porcentaje_completado = $validado['porcentaje_avance'];
                if ($validado['porcentaje_avance'] >= 100) {
                    $tarea->estado = 'completada';
                    $tarea->fecha_completada = now();
                }
                $tarea->save();
            }
        });

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('success', 'Avance registrado exitosamente');
    }

    public function index(Proyecto $proyecto)
    {
        $avances = $proyecto->avancesObra()
            ->with(['supervisor', 'tarea', 'multimedia'])
            ->orderBy('fecha_avance', 'desc')
            ->paginate(10);

        return view('avances.index', compact('proyecto', 'avances'));
    }
}