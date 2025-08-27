<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
    ];

    /**
     * Relasi ke user/tukang yang menawarkan jasa ini
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relasi ke kategori jasa
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Relasi ke rating jasa ini
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'service_id', 'id');
    }
}