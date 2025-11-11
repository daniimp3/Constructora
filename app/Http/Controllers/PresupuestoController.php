<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PresupuestoController extends Controller
{
    // CEO-HU-03: Registrar presupuestos por proyecto
    public function index(Proyecto $proyecto)
    {
        $this->authorize('view', $proyecto);

        $presupuestos = $proyecto->presupuestos()
            ->withSum('gastos', 'monto')
            ->get()
            ->map(function($presupuesto) {
                $presupuesto->variacion = $presupuesto->obtenerVariacion();
                $presupuesto->porcentaje_variacion = $presupuesto->obtenerPorcentajeVariacion();
                return $presupuesto;
            });

        return view('presupuestos.index', compact('proyecto', 'presupuestos'));
    }

    public function store(Request $request, Proyecto $proyecto)
    {
        $this->authorize('update', $proyecto);

        $validado = $request->validate([
            'categoria' => 'required|string|max:255',
            'concepto' => 'required|string|max:255',
            'monto_estimado' => 'required|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        $presupuesto = $proyecto->presupuestos()->create($validado);

        return redirect()
            ->route('presupuestos.index', $proyecto)
            ->with('success', 'Presupuesto registrado exitosamente');
    }

    public function update(Request $request, Proyecto $proyecto, Presupuesto $presupuesto)
    {
        $this->authorize('update', $proyecto);

        $validado = $request->validate([
            'categoria' => 'required|string|max:255',
            'concepto' => 'required|string|max:255',
            'monto_estimado' => 'required|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        $presupuesto->update($validado);

        return redirect()
            ->route('presupuestos.index', $proyecto)
            ->with('success', 'Presupuesto actualizado exitosamente');
    }

    // CEO-HU-05: Generar reportes de gastos vs presupuesto
    public function reporte(Proyecto $proyecto)
    {
        $this->authorize('view', $proyecto);

        $presupuestos = $proyecto->presupuestos()
            ->with('gastos')
            ->get()
            ->map(function($presupuesto) {
                return [
                    'categoria' => $presupuesto->categoria,
                    'concepto' => $presupuesto->concepto,
                    'estimado' => $presupuesto->monto_estimado,
                    'actual' => $presupuesto->monto_actual,
                    'variacion' => $presupuesto->obtenerVariacion(),
                    'porcentaje_variacion' => $presupuesto->obtenerPorcentajeVariacion(),
                    'estado' => $presupuesto->obtenerPorcentajeVariacion() > 100 ? 'excedido' : 'dentro',
                ];
            });

        $resumen = [
            'total_estimado' => $presupuestos->sum('estimado'),
            'total_actual' => $presupuestos->sum('actual'),
            'total_variacion' => $presupuestos->sum('variacion'),
        ];

        return view('presupuestos.reporte', compact('proyecto', 'presupuestos', 'resumen'));
    }
}
