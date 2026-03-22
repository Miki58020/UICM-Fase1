<?php

namespace App\Services;

use App\Mail\NotificacionRol;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificacionService
{
    /**
     * Envía una notificación por correo a todos los usuarios de los roles indicados.
     *
     * @param string|array $roles       Rol o lista de roles destinatarios
     * @param string       $titulo      Asunto y título del correo
     * @param string       $mensaje     Párrafo principal
     * @param array        $detalles    Pares clave => valor para la tabla de detalles
     * @param string|null  $accionUrl   URL del botón de acción (opcional)
     * @param string|null  $accionLabel Texto del botón (opcional)
     */
    public static function enviar(
        string|array $roles,
        string $titulo,
        string $mensaje,
        array $detalles = [],
        ?string $accionUrl = null,
        ?string $accionLabel = null
    ): void {
        $roles = (array) $roles;

        $usuarios = User::whereIn('rol', $roles)->get();

        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->queue(
                new NotificacionRol($usuario, $titulo, $mensaje, $detalles, $accionUrl, $accionLabel)
            );
        }
    }
}
