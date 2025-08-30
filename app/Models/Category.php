<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    protected $with = ['user']; // otomatis load relasi user

    /**
     * Relasi ke jasa (services) yang termasuk kategori ini
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id', 'id');
    }

    /**
     * Relasi ke user yang membuat kategori
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}