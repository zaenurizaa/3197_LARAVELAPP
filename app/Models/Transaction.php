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
        'attendance_status',
    ];

    protected $casts = [
        'is_reserved'    => 'boolean',
        'reserved_until' => 'datetime',
    ];

    /**
     * Boot helper untuk generate ID otomatis di TiDB Cloud
     */
    protected static function booted()
    {
        static::creating(function ($transaction) {
            // Generate id unik berbasis microtime (epoch + random suffix 3 digit)
            // Hasilnya integer panjang yang muat di BIGINT (max 19 digit)
            if (empty($transaction->id)) {
                $transaction->id = (int) (microtime(true) * 1000) . rand(100, 999);
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}