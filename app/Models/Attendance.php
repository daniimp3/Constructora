<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'date',
        'status',
        'time',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'string'
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    // Scope para obtener asistencias de una fecha específica
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    // Scope para obtener asistencias de trabajadores de proyectos de un supervisor
    public function scopeForSupervisor($query, $supervisorId)
    {
        return $query->whereHas('worker.project', function ($q) use ($supervisorId) {
            $q->where('supervisor_id', $supervisorId);
        });
    }

    // Obtener badge de estado
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'present' => '<span class="badge" style="background:#dcfce7;color:#166534">Presente</span>',
            'absent' => '<span class="badge" style="background:#fee2e2;color:#991b1b">Ausente</span>',
            'late' => '<span class="badge" style="background:#fef3c7;color:#92400e">Retardo</span>'
        ];

        return $badges[$this->status] ?? $badges['present'];
    }
}