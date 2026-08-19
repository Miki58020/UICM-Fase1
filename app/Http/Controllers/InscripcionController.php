<?php

namespace App\Http\Controllers;

use App\Mail\BienvenidaAlumno;
use App\Mail\ReenvioCredenciales;
use App\Models\Alumno;
use App\Models\Aspirante;
use App\Models\ConfiguracionCorreo;
use App\Models\Grupo;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\Correo;
use App\Support\Contrasena;

class InscripcionController extends Controller
{
    public function index(Request $request)
    {
        $conteo = [
            'listos'    => Aspirante::where('estado', 'aprobado')
                ->whereHas('pagos', fn($q) => $q->where('estado', 'aprobado'))
                ->whereHas('alumno', fn($q) => $q->whereNull('user_id'))
                ->count(),
            'generados' => Alumno::whereNotNull('user_id')->count(),
        ];

        $vista = $request->query('vista', 'pendientes') === 'generados' ? 'generados' : 'pendientes';
        $programas = Programa::orderBy('nombre')->get();

        if ($vista === 'generados') {
            $generados = Alumno::whereNotNull('user_id')
                ->with(['programa', 'aspirante', 'grupo', 'user'])
                ->when($request->filled('q'), function ($query) use ($request) {
                    $q = $request->q;
                    $query->where(function ($w) use ($q) {
                        $w->where('nombre', 'like', "%{$q}%")
                            ->orWhere('apellido_paterno', 'like', "%{$q}%")
                            ->orWhere('apellido_materno', 'like', "%{$q}%")
                            ->orWhere('matricula', 'like', "%{$q}%")
                            ->orWhereHas('aspirante', fn($w2) => $w2->where('folio', 'like', "%{$q}%"));
                    });
                })
                ->when($request->filled('programa'), fn ($query) => $query->where('programa_id', $request->programa))
                ->latest()
                ->paginate(50)
                ->withQueryString();

            $listos = collect();
        } else {
            $listos = Aspirante::where('estado', 'aprobado')
                ->whereHas('pagos', fn($q) => $q->where('estado', 'aprobado'))
                ->whereHas('alumno', fn($q) => $q->whereNull('user_id'))
                ->with(['programa', 'alumno'])
                ->when($request->filled('q'), function ($query) use ($request) {
                    $q = $request->q;
                    $query->where(function ($w) use ($q) {
                        $w->where('nombre', 'like', "%{$q}%")
                            ->orWhere('apellido_paterno', 'like', "%{$q}%")
                            ->orWhere('apellido_materno', 'like', "%{$q}%")
                            ->orWhere('folio', 'like', "%{$q}%");
                    });
                })
                ->when($request->filled('programa'), fn ($query) => $query->where('programa_id', $request->programa))
                ->latest()
                ->get();

            $generados = collect();
        }

        return view('admin.inscripciones.index', compact('listos', 'generados', 'programas', 'conteo', 'vista'));
    }

    public function inscribir(Request $request, Alumno $alumno)
    {
        if ($alumno->user_id) {
            return redirect()->route('admin.inscripciones.index')
                ->with('error', 'Este alumno ya tiene acceso al portal.');
        }

        // Buscar el primer grupo con espacio disponible para este programa y periodo
        $query = Grupo::where('programa_id', $alumno->programa_id)
            ->withCount('alumnos')
            ->orderBy('id');

        if ($alumno->periodo_id) {
            $query->where('periodo_id', $alumno->periodo_id);
        }

        $grupo = $query->get()->first(fn($g) => $g->alumnos_count < $g->capacidad);

        if (!$grupo) {
            $alumno->load('programa');
            return redirect()->back()->with(
                'error',
                "Sin grupos disponibles para \"{$alumno->programa->nombre}\". Crea un nuevo grupo en la sección de Grupos antes de continuar."
            );
        }

        $password = Contrasena::generar();

        $emailInstitucional = strtolower($alumno->matricula) . '@' . ConfiguracionCorreo::dominio();

        $user = User::create([
            'name'     => $alumno->nombre_completo,
            'email'    => $emailInstitucional,
            'password' => Hash::make($password),
            'rol'      => 'alumno',
        ]);

        $alumno->update([
            'user_id'  => $user->id,
            'grupo_id' => $grupo->id,
        ]);

        $alumno->load(['programa', 'grupo']);

        // La matrícula y el usuario ya se crearon. Si el correo no sale, la
        // pantalla de resultado muestra la contraseña temporal de todas formas.
        $enviado = Correo::enviar($alumno->email, new BienvenidaAlumno($alumno, $password));

        return redirect()->route('admin.inscripciones.resultado', $alumno)
            ->with('temp_password', $password)
            ->with('correo_enviado', $enviado);
    }

    public function resultado(Alumno $alumno)
    {
        $alumno->load(['aspirante', 'programa']);

        return view('admin.inscripciones.generar', [
            'alumno'        => $alumno,
            'temp_password' => session('temp_password'),
        ]);
    }

    public function reenviar(Alumno $alumno)
    {
        if (!$alumno->user_id) {
            return redirect()->back()->with('error', 'Este alumno no tiene acceso al portal aún.');
        }

        $password = Contrasena::generar();

        $alumno->user->update(['password' => Hash::make($password)]);

        $alumno->load('programa');

        if (! Correo::enviar($alumno->email, new ReenvioCredenciales($alumno, $password))) {
            return redirect()->back()->with('error', "La contraseña se restableció, pero no se pudo enviar el correo a {$alumno->email}. Vuelve a intentarlo o entrégala por otro medio.");
        }

        return redirect()->back()->with('success', "Credenciales reenviadas a {$alumno->user->email}.");
    }
}
