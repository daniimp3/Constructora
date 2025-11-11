<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


// ============================================
// MODELO: Proyecto.php
// ============================================
class Proyecto extends Model
{
    use SoftDeletes;

    protected $table = 'proyectos';

    protected $fillable = [
        'nombre', 
        'descripcion', 
        'ubicacion', 
        'fecha_inicio', 
        'fecha_fin_estimada',
        'fecha_fin_real', 
        'estado', 
        'presupuesto_total', 
        'id_administrador', 
        'porcentaje_avance'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin_estimada' => 'date',
        'fecha_fin_real' => 'date',
        'presupuesto_total' => 'decimal:2',
        'porcentaje_avance' => 'decimal:2',
    ];

    // ========== RELACIONES ==========
    
    public function administrador()
    {
        return $this->belongsTo(Usuario::class, 'id_administrador');
    }

    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class, 'id_proyecto');
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'id_proyecto');
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'id_proyecto');
    }

    public function avancesObra()
    {
        return $this->hasMany(AvanceObra::class, 'id_proyecto');
    }

    public function usoMateriales()
    {
        return $this->hasMany(UsoMaterial::class, 'id_proyecto');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_proyecto');
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'id_proyecto');
    }

    public function alertasPresupuesto()
    {
        return $this->hasMany(AlertaPresupuesto::class, 'id_proyecto');
    }

    public function reportes()
    {
        return $this->hasMany(ReporteProyecto::class, 'id_proyecto');
    }

    // ========== SCOPES ==========
    
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeTerminados($query)
    {
        return $query->where('estado', 'terminado');
    }

    // ========== MÉTODOS AUXILIARES ==========
    
    public function obtenerTotalGastos()
    {
        return $this->gastos()->sum('monto');
    }

    public function obtenerVariacionPresupuesto()
    {
        return $this->presupuesto_total - $this->obtenerTotalGastos();
    }

    public function actualizarPorcentajeAvance()
    {
        $totalTareas = $this->tareas()->count();
        if ($totalTareas > 0) {
            $tareasCompletadas = $this->tareas()->where('estado', 'completada')->count();
            $this->porcentaje_avance = ($tareasCompletadas / $totalTareas) * 100;
            $this->save();
        }
    }
}