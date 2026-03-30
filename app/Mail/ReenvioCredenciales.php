<?php

namespace App\Mail;

use App\Models\Alumno;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ReenvioCredenciales extends Mailable
{
    public function __construct(
        public Alumno $alumno,
        public string $password
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Actualización de credenciales — Portal UICM');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.alumno.reenvio-credenciales');
    }
}
