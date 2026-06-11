<?php

namespace App\Http\Middleware;

use App\Models\Alumno;
use App\Models\Profesor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRol
{
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        if (! $request->user()) {
            abort(403);
        }

        $userRol = $request->user()->rol;

        if ($userRol !== 'admin' && $userRol !== $rol) {
            abort(403);
        }

        // Si es alumno, verificar que su cuenta siga activa en cada request
        if ($userRol === 'alumno') {
            $alumno = Alumno::where('user_id', $request->user()->id)->first();

            if ($alumno && $alumno->estado !== 'activo') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $mensaje = $alumno->estado === 'baja'
                    ? 'Tu cuenta ha sido dada de baja. Comunícate con Control Escolar para más información.'
                    : 'Tu cuenta está inactiva temporalmente. Comunícate con Control Escolar para reactivarla.';

                return redirect()->route('login')->withErrors(['email' => $mensaje]);
            }
        }

        // Si es profesor, verificar que su cuenta siga activa en cada request
        if ($userRol === 'profesor') {
            $profesor = Profesor::where('user_id', $request->user()->id)->first();

            if ($profesor && !$profesor->activo) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors(['email' => 'Tu cuenta ha sido desactivada. Comunícate con Coordinación Académica para más información.']);
            }
        }

        return $next($request);
    }
}
