<?php

namespace App\Mail;

use App\Models\Pago;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PagoRechazado extends Mailable
{
    public function __construct(public Pago $pago) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pago rechazado — UICM');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pagos.rechazado');
    }
}
