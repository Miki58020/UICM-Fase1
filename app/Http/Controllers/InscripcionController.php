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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InscripcionController extends Controller
{
    public function index()
    {
        $listos = Aspirante::where('estado', 'aprobado')
            ->whereHas('pagos', fn($q) => $q->where('estado', 'aprobado'))
            ->whereHas('alumno', fn($q) => $q->whereNull('user_id'))
            ->with(['programa', 'alumno'])
            ->latest()
            ->get();

        $inscritos = Alumno::whereNotNull('user_id')->count();

        $generados = Alumno::whereNotNull('user_id')
            ->with(['programa', 'aspirante', 'grupo', 'user'])
            ->latest()
            ->get();

        $programas = Programa::orderBy('nombre')->get();

        return view('admin.inscripciones.index', compact('listos', 'inscritos', 'generados', 'programas'));
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

        $password = Str::random(8);

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
        Mail::to($alumno->email)->send(new BienvenidaAlumno($alumno, $password));

        return redirect()->route('admin.inscripciones.resultado', $alumno)
            ->with('temp_password', $password);
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

        $password = Str::random(8);

        $alumno->user->update(['password' => Hash::make($password)]);

        $alumno->load('programa');
        Mail::to($alumno->email)->send(new ReenvioCredenciales($alumno, $password));

        return redirect()->back()->with('success', "Credenciales reenviadas a {$alumno->user->email}.");
    }
}
