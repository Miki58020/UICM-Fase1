<?php

namespace App\Mail;

use App\Models\Aspirante;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AspiranteRechazado extends Mailable
{
    public function __construct(public Aspirante $aspirante) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Resultado de tu solicitud — UICM');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.aspirantes.rechazado');
    }
}
