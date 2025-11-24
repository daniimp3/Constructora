<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Evidence extends Model
{
    use HasFactory;

    protected $table = 'evidences';


    protected $fillable = [
        'project_id',
        'title',
        'description',
        'photo_path'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Obtener URL completa de la foto
    public function getPhotoUrlAttribute()
    {
        return Storage::url($this->photo_path);
    }

    // Evento: eliminar archivo cuando se elimina el registro
    protected static function booted()
    {
        static::deleting(function ($evidence) {
            if ($evidence->photo_path && Storage::exists($evidence->photo_path)) {
                Storage::delete($evidence->photo_path);
            }
        });
    }
}