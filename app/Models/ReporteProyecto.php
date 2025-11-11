<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// ============================================
// MODELO: ReporteProyecto.php
// ============================================
class ReporteProyecto extends Model
{
    protected $table = 'reportes_proyecto';

    protected $fillable = [
        'id_proyecto', 
        'tipo_reporte', 
        'ruta_archivo', 
        'nombre_archivo',
        'formato', 
        'generado_por', 
        'fecha_reporte_desde', 
        'fecha_reporte_hasta'
    ];

    protected $casts = [
        'fecha_reporte_desde' => 'date',
        'fecha_reporte_hasta' => 'date',
    ];

    // ========== RELACIONES ==========
    
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function generadoPor()
    {
        return $this->belongsTo(Usuario::class, 'generado_por');
    }
}
