<?php

namespace App\Http\Controllers;

use App\Mail\NuevaContrasena;
use App\Models\SolicitudContrasena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SolicitudContrasenaController extends Controller
{
    public function index(Request $request)
    {
        $rol  = auth()->user()->rol;
        $tipo = $request->query('tipo', 'alumnos'); // 'alumnos' | 'administrativos' | 'profesores'

        // Control escolar solo puede ver alumnos
        if ($rol === 'control_escolar') {
            $tipo = 'alumnos';
        }

        return $this->listar($tipo);
    }

    // Ruta dedicada de Coordinación: siempre profesores, sin depender de query string
    // ni del rol de quien la visite (antes un admin la veía vacía/con alumnos por error).
    public function profesores()
    {
        return $this->listar('profesores');
    }

    private function listar(string $tipo)
    {
        $solicitudes = SolicitudContrasena::with('user')
            ->where('estado', 'pendiente')
            ->when($tipo === 'alumnos', fn($q) =>
                $q->whereHas('user', fn($u) => $u->where('rol', 'alumno'))
            )
            ->when($tipo === 'administrativos', fn($q) =>
                $q->whereHas('user', fn($u) => $u->whereNotIn('rol', ['alumno', 'profesor']))
            )
            ->when($tipo === 'profesores', fn($q) =>
                $q->whereHas('user', fn($u) => $u->where('rol', 'profesor'))
            )
            ->latest()
            ->get();

        return view('admin.solicitudes-contrasena.index', compact('solicitudes', 'tipo'));
    }

    public function atender(SolicitudContrasena $solicitud)
    {
        $password = Str::random(8);

        $solicitud->user->update(['password' => Hash::make($password)]);
        $solicitud->update(['estado' => 'atendida']);

        // El correo institucional del alumno es solo su usuario de login (no hay
        // buzón real detrás) -- se le avisa a su correo personal, el mismo que usó
        // durante su admisión/migración.
        $correoAviso = $solicitud->user->rol === 'alumno'
            ? ($solicitud->user->alumno->email ?? $solicitud->user->email)
            : $solicitud->user->email;

        $ruta = $solicitud->user->rol === 'profesor'
            ? 'admin.contrasenas-profesores.index'
            : 'admin.solicitudes-contrasena.index';

        try {
            Mail::to($correoAviso)->send(new NuevaContrasena($solicitud->user, $password));
        } catch (\Throwable $e) {
            // La contraseña ya se cambió; solo el aviso por correo falló (ej. límite
            // del proveedor de correo). No revertir el cambio por eso.
            return redirect()->route($ruta)
                ->with('error', "Contraseña actualizada, pero no se pudo enviar el correo a {$correoAviso}.");
        }

        return redirect()->route($ruta)
            ->with('success', 'Contraseña actualizada y enviada a ' . $correoAviso);
    }
}
