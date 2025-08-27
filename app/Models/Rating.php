<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'user_id',
        'rating',
        'comment',
    ];

    /**
     * Relasi ke jasa yang diberi rating
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    /**
     * Relasi ke user yang memberikan rating
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}