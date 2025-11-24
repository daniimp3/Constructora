<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('project');

        // filtrar por proyecto si se proporciona
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $expenses = $query->orderBy('date', 'desc')->get();
        
        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'concept' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        $expense = Expense::create($validated);

        // verificar si el presupuesto fue excedido
        $project = $expense->project;
        $budgetExceeded = $project->isBudgetExceeded();

        return response()->json([
            'success' => true,
            'message' => 'Gasto registrado correctamente',
            'expense' => $expense->load('project'),
            'budget_exceeded' => $budgetExceeded,
            'project_spent' => $project->spent,
            'project_budget' => $project->budget
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'concept' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        $expense->update($validated);

        // verificar si el presupuesto fue excedido
        $project = $expense->project;
        $budgetExceeded = $project->isBudgetExceeded();

        return response()->json([
            'success' => true,
            'message' => 'Gasto actualizado correctamente',
            'expense' => $expense->load('project'),
            'budget_exceeded' => $budgetExceeded,
            'project_spent' => $project->spent,
            'project_budget' => $project->budget
        ]);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gasto eliminado correctamente'
        ]);
    }

    // obtener gastos por proyecto
    public function byProject(Project $project)
    {
        $expenses = $project->expenses()->orderBy('date', 'desc')->get();
        
        return response()->json([
            'expenses' => $expenses,
            'total' => $project->spent,
            'budget' => $project->budget,
            'budget_exceeded' => $project->isBudgetExceeded(),
            'budget_used_percentage' => $project->getBudgetUsedPercentage()
        ]);
    }
}