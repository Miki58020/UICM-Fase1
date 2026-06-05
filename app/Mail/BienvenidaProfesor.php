<?php

namespace App\Mail;

use App\Models\Profesor;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BienvenidaProfesor extends Mailable
{
    public function __construct(
        public Profesor $profesor,
        public string $password
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Bienvenido al portal UICM — Tus credenciales de acceso');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.profesor.bienvenida');
    }
}
