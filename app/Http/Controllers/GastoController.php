<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class GastoController extends Controller
{
    public function index(Proyecto $proyecto)
    {
        $this->authorize('view', $proyecto);

        $gastos = $proyecto->gastos()
            ->with(['presupuesto', 'registradoPor'])
            ->orderBy('fecha_gasto', 'desc')
            ->paginate(20);

        $totalGastos = $proyecto->gastos()->sum('monto');
        $gastosMensuales = $proyecto->gastos()
            ->whereMonth('fecha_gasto', now()->month)
            ->sum('monto');

        return view('gastos.index', compact('proyecto', 'gastos', 'totalGastos', 'gastosMensuales'));
    }

    public function store(Request $request, Proyecto $proyecto)
    {
        $this->authorize('update', $proyecto);

        $validado = $request->validate([
            'id_presupuesto' => 'nullable|exists:presupuestos,id',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'categoria' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validado['registrado_por'] = Auth::id();

        if ($request->hasFile('comprobante')) {
            $validado['ruta_comprobante'] = $request->file('comprobante')
                ->store('comprobantes/' . $proyecto->id, 'public');
        }

        $gasto = $proyecto->gastos()->create($validado);

        // Actualizar el gasto actual del presupuesto
        if ($gasto->id_presupuesto) {
            $presupuesto = Presupuesto::find($gasto->id_presupuesto);
            $presupuesto->monto_actual += $gasto->monto;
            $presupuesto->save();

            // CEO-HU-15: Crear alerta si se excede el presupuesto
            $this->verificarAlertaPresupuesto($presupuesto, $proyecto);
        }

        return redirect()
            ->route('gastos.index', $proyecto)
            ->with('success', 'Gasto registrado exitosamente');
    }

    private function verificarAlertaPresupuesto(Presupuesto $presupuesto, Proyecto $proyecto)
    {
        $porcentaje = $presupuesto->obtenerPorcentajeVariacion();

        if ($porcentaje >= 90 && $porcentaje < 100) {
            AlertaPresupuesto::create([
                'id_proyecto' => $proyecto->id,
                'id_presupuesto' => $presupuesto->id,
                'porcentaje_umbral' => 90,
                'porcentaje_actual' => $porcentaje,
                'tipo_alerta' => 'advertencia',
                'mensaje' => "El presupuesto '{$presupuesto->concepto}' ha alcanzado el {$porcentaje}% de utilización",
            ]);
        } elseif ($porcentaje >= 100) {
            AlertaPresupuesto::create([
                'id_proyecto' => $proyecto->id,
                'id_presupuesto' => $presupuesto->id,
                'porcentaje_umbral' => 100,
                'porcentaje_actual' => $porcentaje,
                'tipo_alerta' => 'excedido',
                'mensaje' => "¡ALERTA! El presupuesto '{$presupuesto->concepto}' ha excedido el límite ({$porcentaje}%)",
            ]);
        }
    }
}

