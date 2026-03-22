<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NotificacionRol extends Mailable
{
    public function __construct(
        public User    $destinatario,
        public string  $titulo,
        public string  $mensaje,
        public array   $detalles = [],
        public ?string $accionUrl = null,
        public ?string $accionLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[UICM] ' . $this->titulo);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.sistema.notificacion');
    }
}
