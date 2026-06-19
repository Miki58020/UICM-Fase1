<?php

namespace App\Http\Controllers;

use App\Mail\ContrasenaActualizada;
use App\Mail\NuevaContrasena;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\CargaAcademica;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AlumnoController extends Controller
{
    public function listado()
    {
        $alumnos = Alumno::with(['programa', 'grupo', 'user'])
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $programas = \App\Models\Programa::orderBy('nombre')->get();
        $grupos    = \App\Models\Grupo::with('programa')->orderBy('clave')->get();

        $conteo = [
            'total'    => $alumnos->count(),
            'activos'  => $alumnos->where('estado', 'activo')->count(),
            'inactivos'=> $alumnos->where('estado', '!=', 'activo')->count(),
        ];

        return view('admin.alumnos.index', compact('alumnos', 'programas', 'grupos', 'conteo'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'estado'             => 'required|in:activo,inactivo,baja',
            'cuatrimestre_actual'=> 'required|integer|min:1|max:12',
            'grupo_id'           => 'nullable|exists:grupos,id',
            'password'           => 'nullable|string|min:6',
        ], [
            'estado.required'              => 'El estado es obligatorio.',
            'cuatrimestre_actual.required' => 'El cuatrimestre es obligatorio.',
            'cuatrimestre_actual.integer'  => 'El cuatrimestre debe ser un número.',
            'grupo_id.exists'              => 'El grupo seleccionado no es válido.',
            'password.min'                 => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $alumno->update([
            'estado'              => $request->estado,
            'cuatrimestre_actual' => $request->cuatrimestre_actual,
            'grupo_id'            => $request->grupo_id ?: null,
        ]);

        if ($request->filled('password') && $alumno->user_id) {
            $alumno->user->update(['password' => Hash::make($request->password)]);
            Mail::to($alumno->user->email)->send(new NuevaContrasena($alumno->user, $request->password));
        }

        return redirect()->route('admin.alumnos.index')
            ->with('success', "Alumno {$alumno->nombre_completo} actualizado correctamente.");
    }

    public function dashboard()
    {
        $alumno = Alumno::where('user_id', Auth::id())
            ->with(['programa', 'grupo.periodo', 'pagos'])
            ->firstOrFail();

        $carga = $alumno->grupo_id
            ? CargaAcademica::with(['materia', 'profesor'])
                ->where('grupo_id', $alumno->grupo_id)
                ->get()
            : collect();

        $totalCreditos = $carga->sum(fn($c) => $c->materia->creditos ?? 0);

        // Calificaciones del alumno en las materias del período actual
        $calificaciones = collect();
        if ($carga->isNotEmpty()) {
            $cargaIds = $carga->pluck('id');
            $calificaciones = Calificacion::where('alumno_id', $alumno->id)
                ->whereIn('carga_academica_id', $cargaIds)
                ->get()
                ->groupBy('carga_academica_id');
        }

        // Merge inscripción pagos (via aspirante_id) with reinscripción pagos (via alumno_id)
        $pagosReinscripcion = Pago::where('alumno_id', $alumno->id)
            ->where('concepto', 'reinscripcion')
            ->get();
        $pagos = $alumno->pagos->merge($pagosReinscripcion)->sortByDesc('created_at');

        return view('alumno.dashboard', compact('alumno', 'carga', 'totalCreditos', 'calificaciones', 'pagos'));
    }

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

        // Notificar al alumno por email
        Mail::to($user->email)->send(new ContrasenaActualizada($user, $request->password));

        return redirect()->route('alumno.dashboard')->with('password_success', 'Contraseña actualizada correctamente. Se envió confirmación a tu correo.');
    }

    public function checkEstado(): \Illuminate\Http\JsonResponse
    {
        $alumno = Alumno::where('user_id', Auth::id())->first();
        return response()->json(['estado' => $alumno?->estado ?? 'activo']);
    }

    public function kardex()
    {
        $alumno = Alumno::where('user_id', Auth::id())
            ->with(['programa', 'grupo.periodo', 'periodo'])
            ->firstOrFail();

        // Materias del grupo actual (aunque aún no tengan calificación)
        $cargasActuales = $alumno->grupo_id
            ? CargaAcademica::where('grupo_id', $alumno->grupo_id)
                ->with(['materia', 'profesor', 'periodo'])
                ->get()
            : collect();

        // Historial completo: cualquier carga académica (de cualquier cuatrimestre cursado)
        // en la que el alumno tenga al menos una calificación registrada
        $calificaciones = Calificacion::where('alumno_id', $alumno->id)
            ->with('cargaAcademica.materia', 'cargaAcademica.profesor', 'cargaAcademica.periodo')
            ->get()
            ->groupBy('carga_academica_id');

        $cargasHistoricas = $calificaciones->map(fn($grupo) => $grupo->first()->cargaAcademica)->filter();

        $cargas = $cargasActuales->concat($cargasHistoricas)->unique('id');

        $materias = $cargas->map(function ($c) use ($calificaciones) {
            $calif = $calificaciones->get($c->id, collect());
            $p1 = $calif->firstWhere(fn($q) => $q->tipo === 'parcial' && $q->numero === 1);
            $p2 = $calif->firstWhere(fn($q) => $q->tipo === 'parcial' && $q->numero === 2);
            $ex = $calif->firstWhere(fn($q) => $q->tipo === 'extraordinario');
            $calFinal = $ex
                ? $ex->calificacion
                : ($p1 && $p2 ? round(($p1->calificacion + $p2->calificacion) / 2, 1) : null);
            return [
                'carga'    => $c,
                'p1'       => $p1,
                'p2'       => $p2,
                'ex'       => $ex,
                'calFinal' => $calFinal,
                'aprobado' => $calFinal !== null && $calFinal >= 7.0,
            ];
        })->sortBy(fn($m) => $m['carga']->materia->nombre);

        $porCuatrimestre = $materias
            ->groupBy(fn($m) => $m['carga']->materia->cuatrimestre ?? 0)
            ->sortKeys();

        $conCal          = $materias->filter(fn($m) => $m['calFinal'] !== null);
        $promedioGeneral = $conCal->isNotEmpty()
            ? round($conCal->avg(fn($m) => $m['calFinal']), 1)
            : null;

        $aprobadas  = $materias->filter(fn($m) => $m['calFinal'] !== null && $m['aprobado'])->count();
        $reprobadas = $materias->filter(fn($m) => $m['calFinal'] !== null && !$m['aprobado'])->count();
        $pendientes = $materias->filter(fn($m) => $m['calFinal'] === null)->count();

        $totalCreditosPrograma = $alumno->programa?->total_creditos ?? 0;

        return view('alumno.kardex', compact(
            'alumno', 'porCuatrimestre', 'promedioGeneral',
            'aprobadas', 'reprobadas', 'pendientes',
            'totalCreditosPrograma'
        ));
    }

    public function comprobante(Pago $pago)
    {
        $alumno = Alumno::where('user_id', Auth::id())->firstOrFail();

        abort_if($pago->aspirante_id !== $alumno->aspirante_id, 403);

        return view('alumno.comprobante', compact('pago', 'alumno'));
    }
}
