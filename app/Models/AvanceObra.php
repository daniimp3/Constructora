<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


// ============================================
// MODELO: AvanceObra.php
// ============================================
class AvanceObra extends Model
{
    use SoftDeletes;

    protected $table = 'avances_obra';

    protected $fillable = [
        'id_proyecto', 
        'id_tarea', 
        'id_supervisor', 
        'fecha_avance',
        'descripcion', 
        'notas', 
        'porcentaje_avance'
    ];

    protected $casts = [
        'fecha_avance' => 'date',
        'porcentaje_avance' => 'decimal:2',
    ];

    // ========== RELACIONES ==========
    
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'id_tarea');
    }

    public function supervisor()
    {
        return $this->belongsTo(Usuario::class, 'id_supervisor');
    }

    public function multimedia()
    {
        return $this->hasMany(MultimediaAvance::class, 'id_avance_obra');
    }
}