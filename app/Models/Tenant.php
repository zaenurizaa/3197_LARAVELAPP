<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'status',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
    ];

    // Relasi ke User Admin/Pengelola
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relasi ke Acara milik Tenant ini
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}