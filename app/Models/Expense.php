<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'concept',
        'amount',
        'category',
        'date',
        'description'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Evento: cuando se crea/actualiza/elimina un gasto, actualizar el total gastado del proyecto
    protected static function booted()
    {
        static::saved(function ($expense) {
            $expense->updateProjectSpent();
        });

        static::deleted(function ($expense) {
            $expense->updateProjectSpent();
        });
    }

    public function updateProjectSpent()
    {
        $project = $this->project;
        $project->spent = $project->expenses()->sum('amount');
        $project->save();
    }
}