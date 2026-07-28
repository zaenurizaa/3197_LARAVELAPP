<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant; // <-- Trait Isolasi Multi-Tenant

class Event extends Model
{
    use HasFactory, BelongsToTenant; // <-- Pasang BelongsToTenant di sini

    protected $fillable = [
        'tenant_id', // <-- Tambahkan tenant_id
        'title', 
        'slug', 
        'description', 
        'date', 
        'location', 
        'price', 
        'stock', 
        'poster_path', 
        'category_id'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    // Relasi ke Tenant (Organisasi/UKM Pemilik Event)
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tiers()
    {
        return $this->hasMany(EventTier::class);
    }

    /**
     * Mendapatkan kategori/tier tiket yang sedang aktif berdasarkan rentang tanggal saat ini
     */
    public function getActiveTierAttribute()
    {
        $now = now();
        return $this->tiers()
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->orderBy('price', 'asc')
            ->first();
    }

    /**
     * Mendapatkan harga efektif berdasarkan tier aktif atau harga base event jika tidak ada tier
     */
    public function getEffectivePriceAttribute(): int
    {
        if ($this->price == 0 || (isset($this->is_free) && $this->is_free)) {
            return 0;
        }

        $activeTier = $this->active_tier;
        return $activeTier ? (int) $activeTier->price : (int) $this->price;
    }

    /**
     * Mendapatkan nama tier aktif atau 'Regular' jika tidak ada tier
     */
    public function getEffectiveTierNameAttribute(): string
    {
        if ($this->price == 0 || (isset($this->is_free) && $this->is_free)) {
            return 'Gratis';
        }

        $activeTier = $this->active_tier;
        return $activeTier ? $activeTier->name : 'Regular';
    }
}