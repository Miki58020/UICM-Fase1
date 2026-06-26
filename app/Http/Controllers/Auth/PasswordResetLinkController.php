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
            return back()->with('status', $genericMessage);
        }

        // Bloquear si ya hay una solicitud en las últimas 3 horas
        $reciente = SolicitudContrasena::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(3))
            ->exists();

        if ($reciente) {
            return back()->with('status_error', $limiteMessage);
        }

        $esAlumno = $user->rol === 'alumno';

        SolicitudContrasena::create([
            'user_id' => $user->id,
            'estado'  => 'pendiente',
        ]);

        $recipients = $esAlumno
            ? User::where('rol', 'control_escolar')->get()
            : User::where('rol', 'admin')->get();

        try {
            foreach ($recipients as $recipient) {
                Mail::to($recipient->email)->send(new SolicitudContrasenaNotificacion($user));
            }
        } catch (\Exception $e) {
            // El mail falló pero la solicitud ya está guardada, mostrar éxito igual
        }

        return back()->with('status', $genericMessage);
    }
}
