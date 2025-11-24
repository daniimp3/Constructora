<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'quantity',
        'unit',
        'cost',
        'supplier',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Calcular costo total (cantidad * costo unitario)
    public function getTotalCostAttribute()
    {
        return $this->quantity * $this->cost;
    }

    // Obtener unidad formateada
    public function getFormattedUnitAttribute()
    {
        $units = [
            'kg' => 'Kilogramos',
            'm' => 'Metros',
            'm2' => 'Metros cuadrados',
            'm3' => 'Metros cúbicos',
            'pza' => 'Piezas',
            'lt' => 'Litros',
            'ton' => 'Toneladas',
            'bulto' => 'Bultos'
        ];

        return $units[$this->unit] ?? $this->unit;
    }
}