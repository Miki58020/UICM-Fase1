<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Correo
{
    /**
     * Envía una notificación sin que una falla del servicio de correo
     * interrumpa la operación que ya se completó.
     *
     * Los correos del sistema son avisos posteriores a un cambio de estado ya
     * guardado (una solicitud registrada, un pago aprobado, unas credenciales
     * generadas). Si el servidor SMTP no responde, dejar que la excepción suba
     * provocaría un error 500 y el usuario creería que la operación falló,
     * cuando en realidad sí se guardó. Por eso el error se registra en la
     * bitácora y se devuelve false, para que quien llama pueda avisar que el
     * correo no salió sin revertir nada.
     */
    public static function enviar(?string $destinatario, $mailable): bool
    {
        if (! $destinatario) {
            return false;
        }

        try {
            Mail::to($destinatario)->send($mailable);
            return true;
        } catch (\Throwable $e) {
            Log::error('Falló el envío de correo', [
                'destinatario' => $destinatario,
                'tipo'         => is_object($mailable) ? get_class($mailable) : (string) $mailable,
                'error'        => $e->getMessage(),
            ]);
            return false;
        }
    }
}
