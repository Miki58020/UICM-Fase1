<?php

namespace App\Http\Controllers;

use App\Mail\NuevaContrasena;
use App\Models\SolicitudContrasena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
        // Coordinación solo puede ver profesores
        if ($rol === 'coordinacion') {
            $tipo = 'profesores';
        }

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

    public function atender(Request $request, SolicitudContrasena $solicitud)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ], [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $solicitud->user->update(['password' => Hash::make($request->password)]);
        $solicitud->update(['estado' => 'atendida']);

        Mail::to($solicitud->user->email)->send(new NuevaContrasena($solicitud->user, $request->password));

        $ruta = $solicitud->user->rol === 'profesor'
            ? 'admin.contrasenas-profesores.index'
            : 'admin.solicitudes-contrasena.index';

        return redirect()->route($ruta)
            ->with('success', 'Contraseña actualizada y enviada a ' . $solicitud->user->email);
    }
}
