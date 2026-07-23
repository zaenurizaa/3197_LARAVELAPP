<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'transaction_id',
        'event_id',
        'rating',
        'comment'
    ];

    public function transaction()
    {
        return $table = $this->belongsTo(Transaction::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}