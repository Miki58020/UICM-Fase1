<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudContrasenaNotificacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $solicitante)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Solicitud de cambio de contraseña',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-contrasena-notificacion',
            with: [
                'solicitante' => $this->solicitante,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
