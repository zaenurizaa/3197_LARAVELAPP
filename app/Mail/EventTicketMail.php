<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;

class EventTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    // Menentukan tipe data objek secara eksplisit agar bebas dari warning Intelephense
    public \App\Models\Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'E-Ticket Resmi Anda: ' . $this->transaction->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket',
        );
    }
}