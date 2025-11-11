<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// ============================================
// MODELO: Asistencia.php
// ============================================
class Asistencia extends Model
{
    protected $table = 'asistencias';

    protected $fillable = [
        'id_proyecto', 
        'id_usuario', 
        'fecha_asistencia', 
        'hora_entrada',
        'hora_salida', 
        'estado', 
        'notas', 
        'registrado_por'
    ];

    protected $casts = [
        'fecha_asistencia' => 'date',
        'hora_entrada' => 'datetime',
        'hora_salida' => 'datetime',
    ];

    // ========== RELACIONES ==========
    
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function registradoPor()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }

    // ========== MÉTODOS AUXILIARES ==========
    
    public function obtenerHorasTrabajadas()
    {
        if ($this->hora_entrada && $this->hora_salida) {
            return $this->hora_entrada->diffInHours($this->hora_salida);
        }
        return 0;
    }
}