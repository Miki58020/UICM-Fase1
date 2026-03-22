<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaContrasena extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $usuario,
        public string $nuevaContrasena,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu nueva contraseña - UICM',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nueva-contrasena',
            with: [
                'usuario'        => $this->usuario,
                'nuevaContrasena' => $this->nuevaContrasena,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
