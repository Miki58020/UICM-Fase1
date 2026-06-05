<?php

namespace App\Http\Controllers;

use App\Mail\BienvenidaProfesor;
use App\Mail\NuevaContrasena;
use App\Models\Profesor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProfesorController extends Controller
{
    public function index()
    {
        $profesores = Profesor::with('user')->orderBy('nombre')->get();
        return view('admin.profesores.index', compact('profesores'));
    }

    // Genera acceso al portal para un profesor (crea o regenera contraseña)
    public function generarAcceso(Profesor $profesor)
    {
        $password = Str::random(8);

        if ($profesor->user_id) {
            $profesor->user->update(['password' => Hash::make($password)]);
            Mail::to($profesor->correo)->send(new NuevaContrasena($profesor->user, $password));
        } else {
            $user = User::create([
                'name'     => $profesor->nombre,
                'email'    => $profesor->correo,
                'password' => Hash::make($password),
                'rol'      => 'profesor',
            ]);
            $profesor->update(['user_id' => $user->id]);
            $profesor->load('user');
            Mail::to($profesor->correo)->send(new BienvenidaProfesor($profesor, $password));
        }

        return redirect()->route('admin.profesores.index')
            ->with('success', "Acceso generado para {$profesor->nombre}. Se envió correo con credenciales.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:150',
            'correo'       => 'required|email|unique:profesores,correo|unique:users,email',
            'telefono'     => 'nullable|digits:10',
            'especialidad' => 'nullable|string|max:100',
        ], [
            'correo.unique' => 'Este correo ya está registrado en el sistema.',
        ]);

        $password = Str::random(10);

        $user = User::create([
            'name'     => $request->nombre,
            'email'    => $request->correo,
            'password' => Hash::make($password),
            'rol'      => 'profesor',
        ]);

        $profesor = Profesor::create([
            'nombre'       => $request->nombre,
            'correo'       => $request->correo,
            'telefono'     => $request->telefono,
            'especialidad' => $request->especialidad,
            'activo'       => true,
            'user_id'      => $user->id,
        ]);

        Mail::to($profesor->correo)->send(new BienvenidaProfesor($profesor, $password));

        return redirect()->route('admin.profesores.index')
            ->with('success', "Profesor registrado. Se enviaron las credenciales de acceso a {$profesor->correo}.");
    }

    public function update(Request $request, Profesor $profesor)
    {
        $request->validate([
            'nombre'       => 'required|string|max:150',
            'correo'       => 'required|email|unique:profesores,correo,' . $profesor->id,
            'telefono'     => 'nullable|digits:10',
            'especialidad' => 'nullable|string|max:100',
            'password'     => 'nullable|string|min:6',
        ], [
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $profesor->update([
            'nombre'       => $request->nombre,
            'correo'       => $request->correo,
            'telefono'     => $request->telefono,
            'especialidad' => $request->especialidad,
        ]);

        if ($profesor->user) {
            $profesor->user->update([
                'name'  => $request->nombre,
                'email' => $request->correo,
            ]);

            if ($request->filled('password')) {
                $profesor->user->update(['password' => Hash::make($request->password)]);
                Mail::to($profesor->correo)->send(new NuevaContrasena($profesor->user, $request->password));
            }
        }

        return redirect()->route('admin.profesores.index')
            ->with('success', 'Profesor actualizado correctamente.' . ($request->filled('password') ? ' Se notificó la nueva contraseña por correo.' : ''));
    }

    public function toggle(Profesor $profesor)
    {
        $profesor->update(['activo' => !$profesor->activo]);

        return redirect()->back()
            ->with('success', 'Estado del profesor actualizado.');
    }
}
