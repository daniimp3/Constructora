<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


// ============================================
// MODELO: Gasto.php
// ============================================
class Gasto extends Model
{
    use SoftDeletes;

    protected $table = 'gastos';

    protected $fillable = [
        'id_proyecto', 
        'id_presupuesto', 
        'concepto', 
        'monto', 
        'fecha_gasto',
        'categoria', 
        'descripcion', 
        'ruta_comprobante', 
        'registrado_por'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_gasto' => 'date',
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

    public function registradoPor()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}
