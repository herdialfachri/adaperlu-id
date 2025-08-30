<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'changed_by',
        'status',
        'notes',
    ];

    /**
     * Relasi dari tabel ini ke tabel order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Relasi dari tabel ini ke tabel user melalui kolom changed_by
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by', 'id');
    }
}
