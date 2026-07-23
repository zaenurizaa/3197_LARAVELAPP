<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type', // 'percent' atau 'fixed'
        'discount_value',
        'quota',
        'expires_at'
    ];

    // Hubungan ke banyak transaksi yang memakai kupon ini
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}