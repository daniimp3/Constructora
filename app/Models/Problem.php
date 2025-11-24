<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'worker_id',
        'title',
        'description',
        'category',
        'priority',
        'location',
        'read'
    ];

    protected $casts = [
        'read' => 'boolean'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    // Marcar como leído
    public function markAsRead()
    {
        $this->read = true;
        $this->save();
    }

    // Scope para problemas no leídos
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    // Scope para problemas de un supervisor
    public function scopeForSupervisor($query, $supervisorId)
    {
        return $query->whereHas('project', function ($q) use ($supervisorId) {
            $q->where('supervisor_id', $supervisorId);
        });
    }
}