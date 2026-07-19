<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SolicitudContrasenaNotificacion;
use App\Models\SolicitudContrasena;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PasswordResetLinkController extends Controller
{
    /**
     * Recibe la solicitud de "olvidé mi contraseña" desde el modal de login
     * y crea una SolicitudContrasena para que Control Escolar/Admin la atienda.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $genericMessage  = 'Tu solicitud fue recibida. Un administrador te enviará tu nueva contraseña en breve.';
        $limiteMessage   = 'Ya tienes una solicitud reciente. Espera al menos 3 horas antes de enviar otra.';

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->with('success', $genericMessage);
        }

        // Bloquear si ya hay una solicitud en las últimas 3 horas
        $reciente = SolicitudContrasena::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(3))
            ->exists();

        if ($reciente) {
            return back()->with('error', $limiteMessage);
        }

        SolicitudContrasena::create([
            'user_id' => $user->id,
            'estado'  => 'pendiente',
        ]);

        // Misma separación de 3 vías que atiende las solicitudes: alumno -> Control
        // Escolar, profesor -> Coordinación, cualquier otro rol -> Admin general.
        $rolDestino = match ($user->rol) {
            'alumno'   => 'control_escolar',
            'profesor' => 'coordinacion',
            default    => 'admin',
        };

        $recipients = User::where('rol', $rolDestino)->get();

        try {
            foreach ($recipients as $recipient) {
                Mail::to($recipient->email)->send(new SolicitudContrasenaNotificacion($user));
            }
        } catch (\Exception $e) {
            // El mail falló pero la solicitud ya está guardada, mostrar éxito igual
        }

        return back()->with('success', $genericMessage);
    }
}
