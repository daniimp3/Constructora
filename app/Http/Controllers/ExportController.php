<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function exportPDF(Project $project)
{
    // Carga los gastos y trabajadores de una sola vez
    $project->load(['expenses', 'workers', 'supervisor']); 

    $pdf = Pdf::loadView('exports.project-detail', compact('project'))
              ->setPaper('a4', 'portrait');

    return $pdf->download("proyecto_{$project->id}.pdf");
}

    
public function exportExcel(Project $project)
{
    $estados = [
        'active' => 'Activo',
        'completed' => 'Completado',
        'pending' => 'Pendiente',
        'canceled' => 'Cancelado',
    ];

    $estadoEsp = $estados[$project->status] ?? $project->status;
    $expenses = $project->expenses ?? collect();
    $workers = $project->workers ?? collect();
    $totalGasto = $expenses->sum('amount');

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // ANCHO DE COLUMNAS
    foreach(range('A','C') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // ESTILO ENCABEZADOS
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2c3e50']],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    ];

    $sheet->setCellValue('A1', 'Proyecto'); $sheet->setCellValue('B1', $project->name);
    $sheet->setCellValue('A2', 'Cliente'); $sheet->setCellValue('B2', $project->client);
    $sheet->setCellValue('A3', 'Estado'); $sheet->setCellValue('B3', $estadoEsp);
    $sheet->setCellValue('A4', 'Avance (%)'); $sheet->setCellValue('B4', $project->progress ?? 0);
    $sheet->setCellValue('A5', 'Presupuesto Asignado'); $sheet->setCellValue('B5', $project->budget);
    $sheet->setCellValue('A6', 'Gasto Total'); $sheet->setCellValue('B6', $totalGasto);
    $sheet->setCellValue('A7', 'Disponible'); $sheet->setCellValue('B7', $project->budget - $totalGasto);

    // Encabezado financiero
    $sheet->setCellValue('A9', 'Análisis Financiero');
    $sheet->setCellValue('A10', '% Presupuesto Utilizado'); $sheet->setCellValue('B10', ($totalGasto / max($project->budget,1)) * 100);
    $sheet->setCellValue('A11', 'Desviación'); $sheet->setCellValue('B11', $project->budget - $totalGasto);

    // Encabezado de gastos
    $sheet->setCellValue('A13', 'Gastos');
    $sheet->setCellValue('A14', 'Descripción');
    $sheet->setCellValue('B14', 'Monto');
    $sheet->setCellValue('C14', 'Fecha');

    $sheet->getStyle('A14:C14')->applyFromArray($headerStyle);

    $row = 15;
    foreach ($expenses as $e) {
        $sheet->setCellValue("A{$row}", $e->description);
        $sheet->setCellValue("B{$row}", $e->amount);
        $sheet->setCellValue("C{$row}", $e->date ?? '');
        $row++;
    }

    // Personal asignado
    $row += 1;
    $sheet->setCellValue("A{$row}", 'Personal Asignado');
    $row++;
    $sheet->setCellValue("A{$row}", 'Nombre'); $sheet->setCellValue("B{$row}", 'Rol');
    $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($headerStyle);
    $row++;
    foreach ($workers as $w) {
        $sheet->setCellValue("A{$row}", $w->name);
        $sheet->setCellValue("B{$row}", $w->role);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);

    return new StreamedResponse(function () use ($writer) {
        if (ob_get_length()) ob_end_clean();
        $writer->save('php://output');
        flush();
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="proyecto_'.$project->id.'.xlsx"',
    ]);
}
}