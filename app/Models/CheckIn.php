<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    protected $table = 'checkins';

    protected $fillable = [
        'ticket_code',
        'transaction_id',
        'attendee_name',
        'attendee_email',
        'scanner_ip',
        'scanner_user',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
