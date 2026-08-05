<?php

namespace App\Http\Controllers;

use App\Mail\BienvenidaProfesor;
use App\Mail\NuevaContrasena;
use App\Models\Profesor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\Correo;
use Illuminate\Support\Str;

class ProfesorController extends Controller
{
    public function index(Request $request)
    {
        $conteo = [
            'total'    => Profesor::count(),
            'activos'  => Profesor::where('activo', true)->count(),
            'inactivos'=> Profesor::where('activo', false)->count(),
        ];

        $profesores = Profesor::with('user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('correo', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('activo'), fn ($query) => $query->where('activo', $request->activo))
            ->orderBy('nombre')
            ->paginate(50)
            ->withQueryString();

        return view('admin.profesores.index', compact('profesores', 'conteo'));
    }

    // Genera acceso al portal para un profesor (crea o regenera contraseña)
    public function generarAcceso(Profesor $profesor)
    {
        $password = Str::random(8);

        if ($profesor->user_id) {
            $profesor->user->update(['password' => Hash::make($password)]);
            $enviado = Correo::enviar($profesor->correo, new NuevaContrasena($profesor->user, $password));
        } else {
            $user = User::create([
                'name'     => $profesor->nombre,
                'email'    => $profesor->correo,
                'password' => Hash::make($password),
                'rol'      => 'profesor',
            ]);
            $profesor->update(['user_id' => $user->id]);
            $profesor->load('user');
            $enviado = Correo::enviar($profesor->correo, new BienvenidaProfesor($profesor, $password));
        }

        // El acceso ya quedó creado aunque el correo no salga; se avisa para
        // que el personal entregue la contraseña por otro medio.
        return redirect()->route('admin.profesores.index')
            ->with('success', $enviado
                ? "Acceso generado para {$profesor->nombre}. Se envió correo con credenciales."
                : "Acceso generado para {$profesor->nombre}, pero no se pudo enviar el correo a {$profesor->correo}. La contraseña temporal es: {$password}");
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

        $enviado = Correo::enviar($profesor->correo, new BienvenidaProfesor($profesor, $password));

        $redirect = redirect()->route('admin.profesores.index')
            ->with('success', $enviado
                ? "Profesor registrado. Se enviaron las credenciales de acceso a {$profesor->correo}."
                : "Profesor registrado, pero no se pudo enviar el correo a {$profesor->correo}. La contraseña temporal es: {$password}");

        if ($aviso = $this->avisoTelefonoDuplicado($request->telefono, $profesor->id)) {
            $redirect->with('notif_warning_admin', $aviso);
        }

        return $redirect;
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
                $avisoEnviado = Correo::enviar($profesor->correo, new NuevaContrasena($profesor->user, $request->password));
            }
        }

        $notaCorreo = '';
        if ($request->filled('password')) {
            $notaCorreo = ($avisoEnviado ?? false)
                ? ' Se notificó la nueva contraseña por correo.'
                : " No se pudo enviar el aviso a {$profesor->correo}; comunícale la nueva contraseña por otro medio.";
        }

        $redirect = redirect()->route('admin.profesores.index')
            ->with('success', 'Profesor actualizado correctamente.' . $notaCorreo);

        if ($aviso = $this->avisoTelefonoDuplicado($request->telefono, $profesor->id)) {
            $redirect->with('notif_warning_admin', $aviso);
        }

        return $redirect;
    }

    public function toggle(Profesor $profesor)
    {
        $profesor->update(['activo' => !$profesor->activo]);

        return redirect()->back()
            ->with('success', 'Estado del profesor actualizado.');
    }

    // Aviso no bloqueante: un telefono repetido es valido en la vida real (oficina compartida, etc.)
    private function avisoTelefonoDuplicado(?string $telefono, int $exceptId): ?string
    {
        if (!$telefono) {
            return null;
        }

        $duplicado = Profesor::where('telefono', $telefono)
            ->where('id', '!=', $exceptId)
            ->first();

        return $duplicado
            ? "El teléfono {$telefono} ya está registrado para {$duplicado->nombre}. Se guardó de todas formas."
            : null;
    }
}
