<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// ============================================
// MODELO: Notificacion.php
// ============================================
class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'id_usuario', 
        'tipo', 
        'titulo', 
        'mensaje', 
        'datos', 
        'fue_leida', 
        'leida_en'
    ];

    protected $casts = [
        'datos' => 'array',
        'fue_leida' => 'boolean',
        'leida_en' => 'datetime',
    ];

    // ========== RELACIONES ==========
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // ========== SCOPES ==========
    
    public function scopeNoLeidas($query)
    {
        return $query->where('fue_leida', false);
    }

    // ========== MÉTODOS AUXILIARES ==========
    
    public function marcarComoLeida()
    {
        $this->update([
            'fue_leida' => true,
            'leida_en' => now(),
        ]);
    }
}