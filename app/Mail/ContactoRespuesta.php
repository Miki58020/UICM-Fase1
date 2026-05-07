<?php

namespace App\Mail;

use App\Models\Contacto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoRespuesta extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contacto $contacto,
        public string $respuesta
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Respuesta a tu mensaje — UICM',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contacto-respuesta',
            with: [
                'contacto'  => $this->contacto,
                'respuesta' => $this->respuesta,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
