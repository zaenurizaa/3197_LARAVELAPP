<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Transaction extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'event_id', 
        'coupon_id',
        'order_id', 
        'customer_name', 
        'customer_email', 
        'customer_phone', 
        'customer_whatsapp',
        'total_price', 
        'status', 
        'snap_token',
        'is_reserved',      // Wajib ada agar tidak kena mass-assignment protection
        'reserved_until',   // Wajib ada agar tidak kena mass-assignment protection
    ];

    protected $casts = [
        'is_reserved'    => 'boolean',
        'reserved_until' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}