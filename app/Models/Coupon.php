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
        'expires_at',
        'event_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Accessor: Map max_uses to quota
    public function getMaxUsesAttribute()
    {
        return $this->quota;
    }

    // Accessor: Map discount_amount to discount_value
    public function getDiscountAmountAttribute()
    {
        return $this->discount_value;
    }

    // Accessor: Hitung total pemakaian kupon
    public function getUsedCountAttribute()
    {
        return $this->transactions()->count();
    }

    // Hubungan ke banyak transaksi yang memakai kupon ini
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}