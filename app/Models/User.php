<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'project_id',
        'phone',
        'specialty'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relaciones
    public function project()
    {
    return $this->belongsTo(Project::class, 'project_id'); // 'project_id' es la columna en users
    }
    public function temporaryPassword()
    {
        return $this->hasOne(TemporaryPassword::class);
    }    

    

    public function supervisedProjects()
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'worker_id');
    }

    public function problems()
    {
        return $this->hasMany(Problem::class, 'worker_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'worker_id');
    }

    // Scopes
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeSupervisors($query)
    {
        return $query->where('role', 'supervisor');
    }

    public function scopeWorkers($query)
    {
        return $query->where('role', 'trabajador');
    }

    // Helpers
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isWorker()
    {
        return $this->role === 'trabajador';
    }

    // Generar email automático para trabajador
    public static function generateWorkerEmail($name)
    {
        // Remover acentos y caracteres especiales
        $cleanName = strtolower(trim($name));
        $cleanName = iconv('UTF-8', 'ASCII//TRANSLIT', $cleanName);
        $cleanName = preg_replace('/[^a-z0-9]/', '', $cleanName);
        
        // Tomar solo el primer nombre
        $firstName = explode(' ', trim($name))[0];
        $firstName = strtolower($firstName);
        $firstName = iconv('UTF-8', 'ASCII//TRANSLIT', $firstName);
        $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);
        
        return $firstName . 'trabajador@constructora.com';
    }

    // Generar contraseña temporal
    public static function generateTemporaryPassword()
    {
        return 'Temp' . rand(1000, 9999) . '!';
    }
}