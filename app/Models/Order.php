<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'status',
        'start_date',
        'end_date',
        'unit_price',
        'total_price',
        'notes',
        'payment_status',
        'payment_method,'
    ];

    /**
     * Relasi dari tabel ini ke tabel user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relasi dari tabel ini ke tabel service
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    /**
     * Relasi dari tabel order history ke tabel ini
     */
    public function orderHistories()
    {
        return $this->hasMany(OrderHistory::class, 'order_id', 'id');
    }

    /**
     * Relasi dari tabel rating ke tabel ini
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'order_id', 'id');
    }
}