<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// ============================================
// MODELO: MultimediaAvance.php
// ============================================
class MultimediaAvance extends Model
{
    protected $table = 'multimedia_avances';

    protected $fillable = [
        'id_avance_obra', 
        'tipo', 
        'ruta_archivo', 
        'nombre_archivo', 
        'descripcion'
    ];

    // ========== RELACIONES ==========
    
    public function avanceObra()
    {
        return $this->belongsTo(AvanceObra::class, 'id_avance_obra');
    }
}

