<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// ============================================
// MODELO: UsoMaterial.php
// ============================================
class UsoMaterial extends Model
{
    protected $table = 'uso_materiales';

    protected $fillable = [
        'id_proyecto', 
        'id_material', 
        'registrado_por', 
        'cantidad', 
        'fecha_uso', 
        'notas'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'fecha_uso' => 'date',
    ];

    // ========== RELACIONES ==========
    
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material');
    }

    public function registradoPor()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}
