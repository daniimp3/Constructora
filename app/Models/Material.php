<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// ============================================
// MODELO: Material.php
// ============================================
class Material extends Model
{
    use SoftDeletes;

    protected $table = 'materiales';

    protected $fillable = [
        'nombre', 
        'descripcion', 
        'unidad', 
        'costo_unitario', 
        'existencia', 
        'existencia_minima'
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
        'existencia' => 'decimal:2',
        'existencia_minima' => 'decimal:2',
    ];

    // ========== RELACIONES ==========
    
    public function usos()
    {
        return $this->hasMany(UsoMaterial::class, 'id_material');
    }

    // ========== MÉTODOS AUXILIARES ==========
    
    public function tieneBajaExistencia()
    {
        return $this->existencia <= $this->existencia_minima;
    }
}