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
        'service_name',
        'description',
        'price',
        'average_rating',
    ];

    /**
     * Relasi dari tabel ini ke tabel user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relasi dari tabel ini ke tabel category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Relasi dari tabel rating ke tabel ini
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'service_id', 'id');
    }

    /**
     * Relasi dari tabel order ke tabel ini
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'service_id', 'id');
    }
}