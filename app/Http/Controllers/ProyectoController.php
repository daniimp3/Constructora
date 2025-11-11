<?php

namespace App\Http\Controllers;

use App\Models\{Proyecto, Presupuesto, AlertaPresupuesto};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// ============================================
// CONTROLADOR DE PROYECTOS
// ============================================
class ProyectoController extends Controller
{
    // CEO-HU-01: Registrar nuevo proyecto
    public function index()
    {
        $proyectos = Proyecto::with('administrador')
            ->when(Auth::user()->rol === 'administrador', function($query) {
                return $query->where('id_administrador', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('proyectos.index', compact('proyectos'));
    }

    public function create()
    {
        $this->authorize('create', Proyecto::class);
        return view('proyectos.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Proyecto::class);

        $validado = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin_estimada' => 'required|date|after:fecha_inicio',
            'presupuesto_total' => 'required|numeric|min:0',
        ]);

        $validado['id_administrador'] = Auth::id();
        $validado['estado'] = 'activo';

        $proyecto = Proyecto::create($validado);

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('success', 'Proyecto creado exitosamente');
    }

    // CEO-HU-08: Visualizar porcentaje de avance
    // CEO-HU-10: Filtrar proyectos por estado
    public function show(Proyecto $proyecto)
    {
        $this->authorize('view', $proyecto);

        $proyecto->load([
            'presupuestos',
            'tareas.asignadoA',
            'avancesObra.supervisor',
            'gastos',
            'incidencias' => fn($q) => $q->abiertas(),
        ]);

        $totalGastos = $proyecto->obtenerTotalGastos();
        $variacionPresupuesto = $proyecto->obtenerVariacionPresupuesto();
        $tareasCompletadas = $proyecto->tareas()->where('estado', 'completada')->count();
        $totalTareas = $proyecto->tareas()->count();

        return view('proyectos.show', compact(
            'proyecto',
            'totalGastos',
            'variacionPresupuesto',
            'tareasCompletadas',
            'totalTareas'
        ));
    }

    public function edit(Proyecto $proyecto)
    {
        $this->authorize('update', $proyecto);
        return view('proyectos.edit', compact('proyecto'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $this->authorize('update', $proyecto);

        $validado = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin_estimada' => 'required|date|after:fecha_inicio',
            'fecha_fin_real' => 'nullable|date',
            'estado' => 'required|in:activo,terminado,pausado,cancelado',
            'presupuesto_total' => 'required|numeric|min:0',
        ]);

        $proyecto->update($validado);

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('success', 'Proyecto actualizado exitosamente');
    }

    // CEO-HU-12: Comparar proyectos en ejecución
    public function comparar(Request $request)
    {
        $this->authorize('viewAny', Proyecto::class);

        $idsProyectos = $request->input('proyectos', []);

        if (count($idsProyectos) < 2) {
            return back()->with('error', 'Selecciona al menos 2 proyectos para comparar');
        }

        $proyectos = Proyecto::with(['presupuestos', 'gastos', 'tareas'])
            ->whereIn('id', $idsProyectos)
            ->get()
            ->map(function($proyecto) {
                return [
                    'id' => $proyecto->id,
                    'nombre' => $proyecto->nombre,
                    'presupuesto' => $proyecto->presupuesto_total,
                    'gastos' => $proyecto->obtenerTotalGastos(),
                    'variacion' => $proyecto->obtenerVariacionPresupuesto(),
                    'avance' => $proyecto->porcentaje_avance,
                    'tareas_completadas' => $proyecto->tareas()->completadas()->count(),
                    'tareas_total' => $proyecto->tareas()->count(),
                    'fecha_inicio' => $proyecto->fecha_inicio,
                    'fecha_fin_estimada' => $proyecto->fecha_fin_estimada,
                    'estado' => $proyecto->estado,
                ];
            });

        return view('proyectos.comparar', compact('proyectos'));
    }
}