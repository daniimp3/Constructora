<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// ============================================
// MODELO: Incidencia.php
// ============================================
class Incidencia extends Model
{
    use SoftDeletes;

    protected $table = 'incidencias';

    protected $fillable = [
        'id_proyecto', 
        'reportado_por', 
        'titulo', 
        'descripcion', 
        'severidad',
        'estado', 
        'asignado_a', 
        'resuelto_en', 
        'notas_resolucion'
    ];

    protected $casts = [
        'resuelto_en' => 'datetime',
    ];

    // ========== RELACIONES ==========
    
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function reportadoPor()
    {
        return $this->belongsTo(Usuario::class, 'reportado_por');
    }

    public function asignadoA()
    {
        return $this->belongsTo(Usuario::class, 'asignado_a');
    }

    // ========== SCOPES ==========
    
    public function scopeAbiertas($query)
    {
        return $query->whereIn('estado', ['reportado', 'en_revision']);
    }

    public function scopeCriticas($query)
    {
        return $query->where('severidad', 'critica');
    }
}