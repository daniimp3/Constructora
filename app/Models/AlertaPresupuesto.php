<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// ============================================
// MODELO: AlertaPresupuesto.php
// ============================================
class AlertaPresupuesto extends Model
{
    protected $table = 'alertas_presupuesto';

    protected $fillable = [
        'id_proyecto', 
        'id_presupuesto', 
        'porcentaje_umbral', 
        'porcentaje_actual',
        'tipo_alerta', 
        'fue_leida', 
        'mensaje'
    ];

    protected $casts = [
        'porcentaje_umbral' => 'decimal:2',
        'porcentaje_actual' => 'decimal:2',
        'fue_leida' => 'boolean',
    ];

    // ========== RELACIONES ==========
    
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class, 'id_presupuesto');
    }

    // ========== SCOPES ==========
    
    public function scopeNoLeidas($query)
    {
        return $query->where('fue_leida', false);
    }
}