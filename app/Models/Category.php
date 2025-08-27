<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relasi ke jasa (services) yang termasuk kategori ini
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id', 'id');
    }
}