<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


// ============================================
// MODELO: Presupuesto.php
// ============================================
class Presupuesto extends Model
{
    use SoftDeletes;

    protected $table = 'presupuestos';

    protected $fillable = [
        'id_proyecto', 
        'categoria', 
        'concepto', 
        'monto_estimado', 
        'monto_actual', 
        'notas'
    ];

    protected $casts = [
        'monto_estimado' => 'decimal:2',
        'monto_actual' => 'decimal:2',
    ];

    // ========== RELACIONES ==========
    
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'id_presupuesto');
    }

    // ========== MÉTODOS AUXILIARES ==========
    
    public function obtenerVariacion()
    {
        return $this->monto_estimado - $this->monto_actual;
    }

    public function obtenerPorcentajeVariacion()
    {
        if ($this->monto_estimado > 0) {
            return (($this->monto_actual / $this->monto_estimado) * 100);
        }
        return 0;
    }
}

