<?php

namespace App\Http\Controllers;

use App\Mail\NuevaContrasena;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\CargaAcademica;
use App\Models\Grupo;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CalificacionController extends Controller
{
    // Vista del profesor: sus materias asignadas
    public function indexProfesor()
    {
        $profesor = Profesor::where('user_id', Auth::id())->firstOrFail();

        $cargas = CargaAcademica::where('profesor_id', $profesor->id)
            ->with(['materia', 'grupo.programa', 'periodo'])
            ->orderByDesc('periodo_id')
            ->get();

        return view('profesor.calificaciones.index', compact('profesor', 'cargas'));
    }

    // Vista del profesor: alumnos de una materia/grupo para capturar calificaciones
    public function capturar(CargaAcademica $carga)
    {
        $profesor = Profesor::where('user_id', Auth::id())->firstOrFail();

        abort_if($carga->profesor_id !== $profesor->id, 403);

        $alumnos = Alumno::where('grupo_id', $carga->grupo_id)
            ->where('estado', 'activo')
            ->orderBy('apellido_paterno')
            ->get();

        $calificaciones = Calificacion::where('carga_academica_id', $carga->id)
            ->get()
            ->groupBy(fn($c) => $c->alumno_id . '-' . $c->tipo . '-' . $c->numero);

        $carga->load(['materia', 'grupo.programa', 'periodo']);

        return view('profesor.calificaciones.capturar', compact('carga', 'alumnos', 'calificaciones'));
    }

    // Guardar todas las calificaciones en lote
    public function guardar(Request $request, CargaAcademica $carga)
    {
        $profesor = Profesor::where('user_id', Auth::id())->firstOrFail();
        abort_if($carga->profesor_id !== $profesor->id, 403);

        $alumnosDelGrupo = Alumno::where('grupo_id', $carga->grupo_id)
            ->where('estado', 'activo')
            ->pluck('id')
            ->toArray();

        $grades = $request->input('grades', []);
        $errores = [];
        $guardadas = 0;

        foreach ($grades as $alumnoId => $tipos) {
            if (!in_array((int) $alumnoId, $alumnosDelGrupo)) {
                continue;
            }

            foreach ($tipos as $tipo => $numeros) {
                if (!in_array($tipo, ['parcial', 'extraordinario'])) {
                    continue;
                }

                foreach ($numeros as $numero => $valor) {
                    // Ignorar celdas vacías
                    if ($valor === null || $valor === '') {
                        continue;
                    }

                    if (!is_numeric($valor) || $valor < 0 || $valor > 10) {
                        $errores[] = "Calificación inválida para alumno #{$alumnoId} ({$tipo} {$numero}).";
                        continue;
                    }

                    // Extraordinario requiere ambos parciales ya guardados
                    if ($tipo === 'extraordinario') {
                        $parciales = Calificacion::where('carga_academica_id', $carga->id)
                            ->where('alumno_id', $alumnoId)
                            ->where('tipo', 'parcial')
                            ->count();

                        // También contar los que vienen en este mismo envío
                        $p1EnEnvio = isset($grades[$alumnoId]['parcial'][1]) && $grades[$alumnoId]['parcial'][1] !== '';
                        $p2EnEnvio = isset($grades[$alumnoId]['parcial'][2]) && $grades[$alumnoId]['parcial'][2] !== '';
                        $parcialesDisponibles = max($parciales, ($p1EnEnvio ? 1 : 0) + ($p2EnEnvio ? 1 : 0));

                        if ($parcialesDisponibles < 2) {
                            $errores[] = "El extraordinario del alumno #{$alumnoId} requiere ambos parciales capturados.";
                            continue;
                        }
                    }

                    Calificacion::updateOrCreate(
                        [
                            'alumno_id'          => (int) $alumnoId,
                            'carga_academica_id' => $carga->id,
                            'tipo'               => $tipo,
                            'numero'             => (int) $numero,
                        ],
                        ['calificacion' => round((float) $valor, 1)]
                    );

                    $guardadas++;
                }
            }
        }

        if (!empty($errores)) {
            return back()->withErrors([implode(' ', $errores)]);
        }

        return back()->with('success', $guardadas > 0
            ? 'Calificaciones guardadas correctamente.'
            : 'No se realizaron cambios.');
    }

    // Cambio de contraseña del profesor desde su portal
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required'  => 'Ingresa la nueva contraseña.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = Auth::user();
        $user->update(['password' => Hash::make($request->password)]);

        // Notificar al profesor por email
        Mail::to($user->email)->send(new NuevaContrasena($user, $request->password));

        return redirect()->route('profesor.calificaciones.index')
            ->with('success', 'Contraseña actualizada correctamente. Se envió confirmación a tu correo.');
    }

    // Vista de coordinación: ver calificaciones por grupo
    public function indexCoordinacion(Request $request)
    {
        $grupos = Grupo::with('programa', 'periodo')->orderBy('clave')->get();

        $grupo = null;
        $cargasConCalif = collect();

        if ($request->filled('grupo_id')) {
            $grupo = Grupo::with(['programa', 'periodo', 'alumnos' => fn($q) => $q->orderBy('apellido_paterno')])
                ->findOrFail($request->grupo_id);

            $cargasConCalif = CargaAcademica::where('grupo_id', $grupo->id)
                ->with(['materia', 'profesor', 'calificaciones.alumno'])
                ->get();
        }

        return view('admin.calificaciones.index', compact('grupos', 'grupo', 'cargasConCalif'));
    }
}
