<?php

namespace App\Mail;

use App\Models\Aspirante;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RegistroConfirmado extends Mailable
{
    public function __construct(public Aspirante $aspirante) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirmación de registro — UICM');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.aspirantes.registro-confirmado');
    }
}
