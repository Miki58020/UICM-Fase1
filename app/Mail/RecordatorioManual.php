<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RecordatorioManual extends Mailable
{
    public function __construct(
        public User  $destinatario,
        public array $datos,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[UICM] Recordatorio de pendientes — ' . now()->translatedFormat('d \d\e F \d\e Y')
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.sistema.recordatorio-manual');
    }
}
