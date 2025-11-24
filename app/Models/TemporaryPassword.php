<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryPassword extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'password',
        'viewed_at'
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Marcar como vista
    public function markAsViewed()
    {
        $this->viewed_at = now();
        $this->save();
    }

    // Verificar si ya fue vista
    public function hasBeenViewed()
    {
        return !is_null($this->viewed_at);
    }
}