<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaterialController extends Controller
{
    // CEO-HU-07: Registrar materiales utilizados
    public function index()
    {
        $materiales = Material::orderBy('nombre')->paginate(20);
        $bajaExistencia = Material::whereColumn('existencia', '<=', 'existencia_minima')->get();

        return view('materiales.index', compact('materiales', 'bajaExistencia'));
    }

    public function registrarUso(Request $request, Proyecto $proyecto)
    {
        $this->authorize('update', $proyecto);

        $validado = $request->validate([
            'id_material' => 'required|exists:materiales,id',
            'cantidad' => 'required|numeric|min:0',
            'fecha_uso' => 'required|date',
            'notas' => 'nullable|string',
        ]);

        $validado['registrado_por'] = Auth::id();

        DB::transaction(function() use ($validado, $proyecto) {
            $proyecto->usoMateriales()->create($validado);

            // Actualizar inventario
            $material = Material::find($validado['id_material']);
            $material->existencia -= $validado['cantidad'];
            $material->save();
        });

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('success', 'Uso de material registrado exitosamente');
    }
}
