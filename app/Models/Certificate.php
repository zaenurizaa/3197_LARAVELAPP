<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'ticket_code',
        'transaction_id',
        'attendee_name',
        'attendee_email',
        'event_title',
        'event_date',
        'file_path',
        'issued_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'issued_at'  => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
