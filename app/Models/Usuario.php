<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// ============================================
// MODELO: Usuario.php
// ============================================
class Usuario extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre', 
        'email', 
        'contraseña', 
        'rol', 
        'telefono', 
        'esta_activo'
    ];

    protected $hidden = [
        'contraseña', 
        'remember_token'
    ];

    protected $casts = [
        'email_verificado_en' => 'datetime',
        'esta_activo' => 'boolean',
    ];

    // IMPORTANTE: Para autenticación con Laravel
    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    // ========== RELACIONES ==========
    
    public function proyectosAdministrados()
    {
        return $this->hasMany(Proyecto::class, 'id_administrador');
    }

    public function tareasAsignadas()
    {
        return $this->hasMany(Tarea::class, 'asignado_a');
    }

    public function tareasCreadas()
    {
        return $this->hasMany(Tarea::class, 'creado_por');
    }

    public function avancesObra()
    {
        return $this->hasMany(AvanceObra::class, 'id_supervisor');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_usuario');
    }

    public function incidenciasReportadas()
    {
        return $this->hasMany(Incidencia::class, 'reportado_por');
    }

    public function incidenciasAsignadas()
    {
        return $this->hasMany(Incidencia::class, 'asignado_a');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_usuario');
    }

    // ========== SCOPES ==========
    
    public function scopeAdministradores($query)
    {
        return $query->where('rol', 'administrador');
    }

    public function scopeSupervisores($query)
    {
        return $query->where('rol', 'supervisor');
    }

    public function scopeTrabajadores($query)
    {
        return $query->where('rol', 'trabajador');
    }

    public function scopeActivos($query)
    {
        return $query->where('esta_activo', true);
    }
}

