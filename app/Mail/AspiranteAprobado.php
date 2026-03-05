<?php

namespace App\Mail;

use App\Models\Aspirante;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AspiranteAprobado extends Mailable
{
    public function __construct(public Aspirante $aspirante) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Solicitud aprobada — UICM');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.aspirantes.aprobado');
    }
}
