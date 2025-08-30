<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'profile_photo',
        'location',
        'phone',
        'specialization',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke jasa (services) yang dimiliki user/tukang
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'user_id', 'id');
    }

    /**
     * Relasi ke rating yang diberikan user
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'user_id', 'id');
    }

    /**
     * Relasi ke orders yang dibuat user
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    /**
     * Relasi ke histori order yang diubah user/tukang/admin
     */
    public function orderHistories()
    {
        return $this->hasMany(OrderHistory::class, 'changed_by', 'id');
    }
}