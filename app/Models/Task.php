<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'worker_id',
        'title',
        'description',
        'status',
        'priority',
        'deadline'
    ];

    protected $casts = [
        'deadline' => 'date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    // Evento: cuando una tarea cambia de estado, actualizar el progreso del proyecto
    protected static function booted()
    {
        static::saved(function ($task) {
            $task->project->updateProgressFromTasks();
        });

        static::deleted(function ($task) {
            $task->project->updateProgressFromTasks();
        });
    }
}