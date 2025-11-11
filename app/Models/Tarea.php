<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// ============================================
// MODELO: Tarea.php
// ============================================
class Tarea extends Model
{
    use SoftDeletes;

    protected $table = 'tareas';

    protected $fillable = [
        'id_proyecto', 
        'nombre', 
        'descripcion', 
        'estado', 
        'prioridad',
        'fecha_inicio', 
        'fecha_vencimiento', 
        'fecha_completada', 
        'asignado_a',
        'creado_por', 
        'porcentaje_completado'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_completada' => 'date',
        'porcentaje_completado' => 'decimal:2',
    ];

    // ========== RELACIONES ==========
    
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function asignadoA()
    {
        return $this->belongsTo(Usuario::class, 'asignado_a');
    }

    public function creadoPor()
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    public function avancesObra()
    {
        return $this->hasMany(AvanceObra::class, 'id_tarea');
    }

    // ========== SCOPES ==========
    
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnProgreso($query)
    {
        return $query->where('estado', 'en_progreso');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    // ========== MÉTODOS AUXILIARES ==========
    
    public function estaVencida()
    {
        return $this->fecha_vencimiento < now() && $this->estado !== 'completada';
    }
}