<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client',
        'description',
        'budget',
        'spent',
        'start_date',
        'progress',
        'status',
        'supervisor_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'progress' => 'integer'
    ];

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function problems()
    {
        return $this->hasMany(Problem::class);
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function workers()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->where('role', 'trabajador'); // solo trabajadores
    }
    public function updateSpent()
{
    $this->spent = $this->expenses()->sum('amount');
    $this->save();
}

    // Calcular progreso automático basado en tareas
    public function updateProgressFromTasks()
    {
        $totalTasks = $this->tasks()->count();
        
        if ($totalTasks === 0) {
            $this->progress = 0;
        } else {
            $completedTasks = $this->tasks()->where('status', 'completed')->count();
            $this->progress = round(($completedTasks / $totalTasks) * 100);
        }
        
        $this->save();
    }

    // Verificar si el presupuesto está excedido
    public function isBudgetExceeded()
    {
        return $this->spent > $this->budget;
    }

    // Obtener porcentaje de presupuesto usado
    public function getBudgetUsedPercentage()
    {
        if ($this->budget == 0) return 0;
        return round(($this->spent / $this->budget) * 100, 1);
    }

    public function getEstadoEspAttribute()
{
    return match($this->status) {
        'active' => 'Activo',
        'completed' => 'Completado',
        'pending' => 'Pendiente',
        'canceled' => 'Cancelado',
        default => $this->status,
    };
}

}