<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReporteController extends Controller
{
    // CEO-HU-14: Exportar reportes en PDF/Excel
    public function generar(Request $request, Proyecto $proyecto)
    {
        $this->authorize('view', $proyecto);

        $validado = $request->validate([
            'tipo_reporte' => 'required|in:gastos,avance,general,comparativo',
            'formato' => 'required|in:pdf,excel',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after:fecha_desde',
        ]);

        $datosReporte = $this->obtenerDatosReporte($proyecto, $validado);

        if ($validado['formato'] === 'pdf') {
            return $this->generarPDF($proyecto, $datosReporte, $validado['tipo_reporte']);
        } else {
            return $this->generarExcel($proyecto, $datosReporte, $validado['tipo_reporte']);
        }
    }

    private function obtenerDatosReporte(Proyecto $proyecto, $params)
    {
        $datos = [
            'proyecto' => $proyecto,
            'generado_en' => now(),
            'fecha_desde' => $params['fecha_desde'] ?? null,
            'fecha_hasta' => $params['fecha_hasta'] ?? null,
        ];

        switch ($params['tipo_reporte']) {
            case 'gastos':
                $datos['gastos'] = $proyecto->gastos()
                    ->when($params['fecha_desde'], fn($q) => $q->whereDate('fecha_gasto', '>=', $params['fecha_desde']))
                    ->when($params['fecha_hasta'], fn($q) => $q->whereDate('fecha_gasto', '<=', $params['fecha_hasta']))
                    ->with('presupuesto')
                    ->get();
                $datos['total'] = $datos['gastos']->sum('monto');
                break;

            case 'avance':
                $datos['tareas'] = $proyecto->tareas()->with('asignadoA')->get();
                $datos['avances'] = $proyecto->avancesObra()->get();
                $datos['porcentaje_avance'] = $proyecto->porcentaje_avance;
                break;

            case 'general':
                $datos['presupuestos'] = $proyecto->presupuestos()->get();
                $datos['gastos'] = $proyecto->gastos()->get();
                $datos['tareas'] = $proyecto->tareas()->get();
                $datos['incidencias'] = $proyecto->incidencias()->get();
                break;
        }

        return $datos;
    }

    private function generarPDF($proyecto, $datos, $tipo)
    {
        // Usar Laravel DomPDF o similar
        $pdf = \PDF::loadView("reportes.{$tipo}", $datos);
        $nombreArchivo = "{$proyecto->nombre}_{$tipo}_" . now()->format('Y-m-d') . ".pdf";
        
        return $pdf->download($nombreArchivo);
    }

    private function generarExcel($proyecto, $datos, $tipo)
    {
        // Usar Laravel Excel
        $nombreArchivo = "{$proyecto->nombre}_{$tipo}_" . now()->format('Y-m-d') . ".xlsx";
        
        return \Excel::download(new ExportacionReporteProyecto($datos, $tipo), $nombreArchivo);
    }
}
