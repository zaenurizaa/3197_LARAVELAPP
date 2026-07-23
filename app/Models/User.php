<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'google_id', // 🔥 Login Google SSO
        'avatar',    // 🔥 Foto profil Google
    ];

    /**
     * Kolom yang disembunyikan saat dikonversi ke Array / JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting atribut.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Relasi ke Organisasi / Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Helper Methods
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }
}