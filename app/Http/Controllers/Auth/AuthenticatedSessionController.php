<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Verificar estado del alumno antes de permitir el acceso
        if (Auth::user()->rol === 'alumno') {
            $alumno = \App\Models\Alumno::where('user_id', Auth::id())->first();

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

        $request->session()->regenerate();

        $rol = Auth::user()->rol;

        $destino = match ($rol) {
            'alumno'  => route('alumno.dashboard'),
            'profesor' => route('profesor.calificaciones.index'),
            default   => route('dashboard'),
        };

        session()->flash('uicm_init', true);

        return redirect()->intended($destino);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
