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
        'category_id', 
        'user_id'
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
    // Pada migration events & transactions
}