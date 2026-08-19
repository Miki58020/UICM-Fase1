<?php

namespace App\Http\Controllers;

use App\Mail\NuevaContrasena;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Support\Correo;
use Illuminate\Validation\Rule;
use App\Support\Contrasena;

class UsuarioController extends Controller
{
    public const ROLES = ['admin', 'control_escolar', 'finanzas', 'coordinacion'];

    public function index(Request $request)
    {
        $conteo = collect(self::ROLES)->mapWithKeys(
            fn($rol) => [$rol => User::where('rol', $rol)->count()]
        );

        $usuarios = User::whereIn('rol', self::ROLES)
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('apellido_paterno', 'like', "%{$q}%")
                        ->orWhere('apellido_materno', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('rol'), fn ($query) => $query->where('rol', $request->rol))
            ->orderBy('rol')
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'conteo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'email'            => 'required|email|unique:users,email',
            'password'         => ['required', 'string', Contrasena::politica()],
            'rol'              => ['required', Rule::in(self::ROLES)],
        ], [
            'name.required'             => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'email.required'            => 'El correo es obligatorio.',
            'email.unique'              => 'Ese correo ya está registrado.',
            'password.required'         => 'La contraseña es obligatoria.',
            'rol.required'              => 'Selecciona un rol.',
        ]);

        User::create([
            'name'             => $request->name,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'rol'              => $request->rol,
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'email'            => ['required', 'email', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password'         => ['nullable', 'string', Contrasena::politica()],
            'rol'              => ['required', Rule::in(self::ROLES)],
        ], [
            'name.required'             => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'email.required'            => 'El correo es obligatorio.',
            'email.unique'              => 'Ese correo ya está registrado.',
            'rol.required'              => 'Selecciona un rol.',
        ]);

        // Si cambia de rol, verificar que no sea el último de su rol actual
        if ($request->rol !== $usuario->rol) {
            $restantes = User::where('rol', $usuario->rol)->where('id', '!=', $usuario->id)->count();
            if ($restantes === 0) {
                return redirect()->route('admin.usuarios.index')
                    ->with('error', "No se puede cambiar el rol: es el único usuario con rol '{$usuario->rol}'.");
            }
        }

        $datos = [
            'name'             => $request->name,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email'            => $request->email,
            'rol'              => $request->rol,
        ];

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        $notaCorreo = '';
        if ($request->filled('password')) {
            $notaCorreo = Correo::enviar($usuario->email, new NuevaContrasena($usuario, $request->password))
                ? ' Se notificó la nueva contraseña por correo.'
                : " No se pudo enviar el aviso a {$usuario->email}; comunícale la nueva contraseña por otro medio.";
        }

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.' . $notaCorreo);
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $restantes = User::where('rol', $usuario->rol)->where('id', '!=', $usuario->id)->count();
        if ($restantes === 0) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', "No se puede eliminar: es el único usuario con rol '{$usuario->rol}'.");
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado.');
    }
}
