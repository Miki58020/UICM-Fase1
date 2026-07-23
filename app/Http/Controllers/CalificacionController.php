<?php

namespace App\Http\Controllers;

use App\Mail\ContrasenaActualizada;
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
    public function indexProfesor(Request $request)
    {
        $profesor = Profesor::where('user_id', Auth::id())->firstOrFail();

        // Prioriza lo que necesita atención del profesor: rechazado y sin enviar primero,
        // luego lo que ya está en revisión, y al final lo ya aprobado (sin acción pendiente).
        $prioridadEstado = ['rechazado' => 0, null => 1, 'pendiente' => 2, 'aprobado' => 3];

        $todasLasCargas = CargaAcademica::where('profesor_id', $profesor->id)
            ->with(['materia', 'grupo.programa', 'grupo.alumnos', 'periodo', 'calificaciones'])
            ->orderByDesc('periodo_id')
            ->get()
            ->sortBy(fn ($c) => $prioridadEstado[$c->estado_revision] ?? 1)
            ->values();

        $conteo = [
            'total'      => $todasLasCargas->count(),
            'aprobado'   => $todasLasCargas->where('estado_revision', 'aprobado')->count(),
            'pendiente'  => $todasLasCargas->where('estado_revision', 'pendiente')->count(),
            'rechazado'  => $todasLasCargas->where('estado_revision', 'rechazado')->count(),
            'sin_enviar' => $todasLasCargas->whereNull('estado_revision')->count(),
        ];

        $cargas = $todasLasCargas
            ->when($request->filled('q'), function ($col) use ($request) {
                $q = mb_strtolower($request->q);
                return $col->filter(fn ($c) => str_contains(mb_strtolower(($c->grupo->clave ?? '').' '.($c->materia->nombre ?? '').' '.($c->materia->clave ?? '')), $q));
            })
            ->when($request->filled('periodo'), fn ($col) => $col->where('periodo_id', (int) $request->periodo))
            ->when($request->filled('estado'), function ($col) use ($request) {
                return $request->estado === 'sin_enviar'
                    ? $col->whereNull('estado_revision')
                    : $col->where('estado_revision', $request->estado);
            })
            ->values();

        $pagina = (int) $request->query('page', 1);
        $porPagina = 50;
        $cargas = new \Illuminate\Pagination\LengthAwarePaginator(
            $cargas->forPage($pagina, $porPagina)->values(),
            $cargas->count(),
            $porPagina,
            $pagina,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('profesor.calificaciones.index', compact('profesor', 'cargas', 'conteo', 'todasLasCargas'));
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
            ->groupBy(fn($c) => $c->alumno_id . '-' . $c->tipo);

        $carga->load(['materia', 'grupo.programa', 'periodo']);

        $puedeCapturar = $carga->puedeCapturar();

        return view('profesor.calificaciones.capturar', compact('carga', 'alumnos', 'calificaciones', 'puedeCapturar'));
    }

    // Guardar todas las calificaciones en lote
    public function guardar(Request $request, CargaAcademica $carga)
    {
        $profesor = Profesor::where('user_id', Auth::id())->firstOrFail();
        abort_if($carga->profesor_id !== $profesor->id, 403);

        if (!$carga->puedeCapturar()) {
            $motivo = match ($carga->estado_revision) {
                'pendiente' => 'Estas calificaciones ya están en revisión por Coordinación; no puedes modificarlas hasta que se resuelvan.',
                'aprobado'  => 'Estas calificaciones ya fueron aprobadas por Coordinación; usa "Solicitar aclaración" para corregirlas.',
                default     => 'La ventana de captura para esta materia ya cerró.',
            };
            return back()->withErrors([$motivo]);
        }

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

            foreach ($tipos as $tipo => $valor) {
                if (!in_array($tipo, ['final', 'extraordinario'])) {
                    continue;
                }

                // Celda vacía: si ya existía una calificación guardada, se borra —el profesor
                // la está limpiando a propósito— para no dejar un valor viejo "fantasma".
                if ($valor === null || $valor === '') {
                    Calificacion::where('carga_academica_id', $carga->id)
                        ->where('alumno_id', (int) $alumnoId)
                        ->where('tipo', $tipo)
                        ->delete();
                    continue;
                }

                if (!is_numeric($valor) || $valor < 0 || $valor > 10) {
                    $errores[] = "Calificación inválida para alumno #{$alumnoId} ({$tipo}).";
                    continue;
                }

                // Extraordinario solo aplica si la calificación final fue reprobatoria
                if ($tipo === 'extraordinario') {
                    $final = Calificacion::where('carga_academica_id', $carga->id)
                        ->where('alumno_id', $alumnoId)
                        ->where('tipo', 'final')
                        ->first();

                    $finalEnEnvio = $grades[$alumnoId]['final'] ?? null;
                    $valorFinal = $final?->calificacion ?? (is_numeric($finalEnEnvio) ? (float) $finalEnEnvio : null);

                    if ($valorFinal === null || $valorFinal >= 7.0) {
                        $errores[] = "El extraordinario del alumno #{$alumnoId} requiere una calificación final reprobatoria.";
                        continue;
                    }
                }

                Calificacion::updateOrCreate(
                    [
                        'alumno_id'          => (int) $alumnoId,
                        'carga_academica_id' => $carga->id,
                        'tipo'               => $tipo,
                    ],
                    ['calificacion' => round((float) $valor, 1)]
                );

                $guardadas++;
            }
        }

        if (!empty($errores)) {
            return back()->withErrors([implode(' ', $errores)]);
        }

        if ($guardadas > 0) {
            // Si estaba rechazado, vuelve a borrador para que el profe lo reenvíe explícitamente
            if ($carga->estado_revision === 'rechazado') {
                $carga->update([
                    'estado_revision' => null,
                    'motivo_rechazo'  => null,
                    'revisado_por'    => null,
                    'revisado_at'     => null,
                ]);
            }

            $this->recalcularCreditos($alumnosDelGrupo);
        }

        // El botón "Enviar a revisión" envía el formulario completo (con las calificaciones
        // recién tecleadas) marcando esta bandera, para que nunca se pueda mandar a revisión
        // un valor viejo que no se había guardado todavía.
        if ($request->boolean('enviar_a_revision')) {
            $alumnosSinFinal = Alumno::whereIn('id', $alumnosDelGrupo)
                ->whereDoesntHave('calificaciones', fn($q) => $q->where('carga_academica_id', $carga->id)->where('tipo', 'final'))
                ->get()
                ->map(fn($a) => $a->nombre_completo);

            if ($alumnosSinFinal->isNotEmpty()) {
                return back()->withErrors([
                    'Falta la calificación final de: ' . $alumnosSinFinal->implode(', ') . '. Debes capturarla para todos los alumnos antes de enviar a revisión.'
                ]);
            }

            $carga->update([
                'estado_revision' => 'pendiente',
                'motivo_rechazo'  => null,
                'revisado_por'    => null,
                'revisado_at'     => null,
            ]);

            return redirect()->route('profesor.calificaciones.index')
                ->with('success', 'Calificaciones guardadas y enviadas a Coordinación para revisión.');
        }

        return back()->with('success', $guardadas > 0
            ? 'Borrador guardado. Cuando termines, usa "Enviar a revisión" para mandarlo a Coordinación.'
            : 'No se realizaron cambios.');
    }

    // Recalcula creditos_acumulados de los alumnos dados a partir de sus calificaciones aprobadas por control escolar
    private function recalcularCreditos(array $alumnoIds): void
    {
        Alumno::whereIn('id', $alumnoIds)->get()
            ->each(fn (Alumno $alumno) => $alumno->recalcularCreditosAcumulados());
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
        Mail::to($user->email)->send(new ContrasenaActualizada($user, $request->password));

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
                ->get()
                ->map(function ($carga) {
                    $finales = $carga->calificaciones->where('tipo', 'final')->pluck('calificacion');
                    $carga->sospechosa = $finales->count() >= 3 && $finales->unique()->count() === 1;

                    return $carga;
                });
        }

        return view('admin.calificaciones.index', compact('grupos', 'grupo', 'cargasConCalif'));
    }

    // Control escolar aprueba las calificaciones capturadas de una materia/grupo
    public function aprobar(CargaAcademica $carga)
    {
        $carga->update([
            'estado_revision' => 'aprobado',
            'motivo_rechazo'  => null,
            'revisado_por'    => Auth::id(),
            'revisado_at'     => now(),
        ]);

        $this->recalcularCreditos(
            Alumno::where('grupo_id', $carga->grupo_id)->pluck('id')->toArray()
        );

        return back()->with('success', 'Calificaciones aprobadas. Ya son visibles para los alumnos.');
    }

    // Control escolar rechaza las calificaciones capturadas; el profesor podrá volver a capturarlas
    public function rechazar(Request $request, CargaAcademica $carga)
    {
        $request->validate(['motivo' => 'required|string|max:500'], [
            'motivo.required' => 'Indica el motivo del rechazo.',
        ]);

        $carga->update([
            'estado_revision' => 'rechazado',
            'motivo_rechazo'  => $request->motivo,
            'revisado_por'    => Auth::id(),
            'revisado_at'     => now(),
        ]);

        $this->recalcularCreditos(
            Alumno::where('grupo_id', $carga->grupo_id)->pluck('id')->toArray()
        );

        return back()->with('success', 'Calificaciones rechazadas. El profesor podrá volver a capturarlas.');
    }
}
