<?php

namespace App\Http\Controllers;

use App\Mail\ContrasenaActualizada;
use App\Mail\NuevaContrasena;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\CargaAcademica;
use App\Models\DocumentoAlumno;
use App\Models\Pago;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AlumnoController extends Controller
{
    public function listado(Request $request)
    {
        $conteo = [
            'total'     => Alumno::count(),
            'activos'   => Alumno::where('estado', 'activo')->count(),
            'inactivos' => Alumno::where('estado', 'inactivo')->count(),
            'baja'      => Alumno::where('estado', 'baja')->count(),
            'migrados'  => Alumno::where('migrado', true)->count(),
        ];

        $alumnos = Alumno::with(['programa', 'grupo', 'user'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('apellido_paterno', 'like', "%{$q}%")
                        ->orWhere('apellido_materno', 'like', "%{$q}%")
                        ->orWhere('matricula', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('programa'), fn ($query) => $query->where('programa_id', $request->programa))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->estado))
            ->when($request->filled('migrado'), fn ($query) => $query->where('migrado', true))
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->paginate(50)
            ->withQueryString();

        $programas = \App\Models\Programa::orderBy('nombre')->get();
        $grupos    = \App\Models\Grupo::with('programa')->orderBy('clave')->get();

        return view('admin.alumnos.index', compact('alumnos', 'programas', 'grupos', 'conteo'));
    }

    // Vista del profesor: alumnos activos de sus grupos asignados
    public function profesor()
    {
        $profesor = Profesor::where('user_id', Auth::id())->firstOrFail();

        $cargas = CargaAcademica::where('profesor_id', $profesor->id)
            ->with(['materia', 'grupo' => fn ($q) => $q->with(['programa', 'alumnos' => fn ($q2) => $q2->where('estado', 'activo')])])
            ->get();

        $alumnos = collect();
        foreach ($cargas as $carga) {
            foreach ($carga->grupo?->alumnos ?? [] as $alumno) {
                if (! $alumnos->has($alumno->id)) {
                    $alumnos->put($alumno->id, (object) [
                        'alumno'   => $alumno,
                        'grupo'    => $carga->grupo,
                        'materias' => collect(),
                    ]);
                }
                $alumnos->get($alumno->id)->materias->push($carga->materia);
            }
        }

        $alumnos = $alumnos->values()->sortBy(fn ($a) => $a->alumno->apellido_paterno);
        $totalGrupos = $cargas->pluck('grupo_id')->unique()->count();

        return view('profesor.alumnos.index', compact('alumnos', 'totalGrupos'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'estado'             => 'required|in:activo,inactivo,baja',
            'cuatrimestre_actual'=> ['required', 'integer', 'min:1', $this->reglaCuatrimestreValido($request, $alumno)],
            'grupo_id'           => 'nullable|exists:grupos,id',
            'password'           => 'nullable|string|min:6',
        ], [
            'estado.required'              => 'El estado es obligatorio.',
            'cuatrimestre_actual.required' => 'El cuatrimestre es obligatorio.',
            'cuatrimestre_actual.integer'  => 'El cuatrimestre debe ser un número.',
            'grupo_id.exists'              => 'El grupo seleccionado no es válido.',
            'password.min'                 => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        // El grupo manda: si hay uno asignado, el cuatrimestre del alumno siempre
        // coincide con el del grupo (evita que queden desincronizados).
        $grupo = $request->filled('grupo_id') ? \App\Models\Grupo::find($request->grupo_id) : null;

        $alumno->update([
            'estado'              => $request->estado,
            'cuatrimestre_actual' => $grupo?->cuatrimestre ?? $request->cuatrimestre_actual,
            'grupo_id'            => $request->grupo_id ?: null,
        ]);

        $avisoPassword = null;
        if ($request->filled('password') && $alumno->user_id) {
            $alumno->user->update(['password' => Hash::make($request->password)]);
            // Correo personal, no el institucional: ese es solo su usuario de login.
            $correoAviso = $alumno->email ?? $alumno->user->email;
            try {
                Mail::to($correoAviso)->send(new NuevaContrasena($alumno->user, $request->password));
            } catch (\Throwable $e) {
                $avisoPassword = " La contraseña se cambió, pero no se pudo enviar el correo a {$correoAviso}.";
            }
        }

        return redirect()->route('admin.alumnos.index')
            ->with('success', "Alumno {$alumno->nombre_completo} actualizado correctamente." . $avisoPassword);
    }

    // Genera una contraseña nueva y la reenvía al alumno (ej. si el correo original
    // nunca le llegó, como puede pasar en la carga masiva por CSV).
    public function reenviarAcceso(Alumno $alumno)
    {
        if (!$alumno->user_id) {
            return redirect()->back()->with('error', 'Este alumno todavía no tiene acceso al portal.');
        }

        $password = \Illuminate\Support\Str::random(8);
        $alumno->user->update(['password' => Hash::make($password)]);

        // Correo personal, no el institucional: si perdió el acceso al portal,
        // el institucional no es un buzón real que pueda consultar.
        $correoAviso = $alumno->email ?? $alumno->user->email;
        try {
            Mail::to($correoAviso)->send(new \App\Mail\ReenvioCredenciales($alumno, $password));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', "La contraseña se generó, pero no se pudo enviar el correo a {$correoAviso}.");
        }

        return redirect()->back()->with('success', "Credenciales reenviadas a {$correoAviso}.");
    }

    private function reglaCuatrimestreValido(Request $request, Alumno $alumno)
    {
        return function (string $attribute, $value, \Closure $fail) use ($request, $alumno) {
            // Si hay grupo, el valor enviado se ignora y se toma del grupo (ver update()).
            if ($request->filled('grupo_id')) {
                return;
            }

            $programa = $alumno->programa;
            if ($programa && (int) $value > $programa->duracion_cuatrimestres) {
                $fail("El programa del alumno dura {$programa->duracion_cuatrimestres} cuatrimestres.");
            }
        };
    }

    public function dashboard()
    {
        $alumno = Alumno::where('user_id', Auth::id())
            ->with(['programa', 'grupo.periodo', 'pagos', 'documentos'])
            ->firstOrFail();

        $carga = $alumno->grupo_id
            ? CargaAcademica::with(['materia', 'profesor'])
                ->where('grupo_id', $alumno->grupo_id)
                ->get()
            : collect();

        $totalCreditos = $carga->sum(fn($c) => $c->materia->creditos ?? 0);

        $pagos = $alumno->todosLosPagos()->sortByDesc('created_at');

        // Promedio general desde calificaciones aprobadas por control escolar
        $todasCalif = Calificacion::where('alumno_id', $alumno->id)
            ->with('cargaAcademica.materia')
            ->get()
            ->filter(fn($c) => $c->cargaAcademica?->estado_revision === 'aprobado');

        $calFinals = $todasCalif->groupBy('carga_academica_id')->map(function ($grupo) {
            $ex    = $grupo->firstWhere('tipo', 'extraordinario');
            $final = $grupo->firstWhere('tipo', 'final');
            return $ex ? $ex->calificacion : $final?->calificacion;
        })->filter(fn($v) => $v !== null);

        $promedioGeneral = $calFinals->isNotEmpty() ? round($calFinals->avg(), 1) : null;

        // Panel: calificaciones recientes (la más reciente capturada por materia)
        $calificacionesRecientes = $todasCalif->groupBy('carga_academica_id')
            ->map(fn($grupo) => $grupo->firstWhere('tipo', 'extraordinario') ?? $grupo->firstWhere('tipo', 'final'))
            ->filter()
            ->sortByDesc('updated_at')
            ->take(5)
            ->values();

        // Estado de pagos
        $pagosPendientes  = $pagos->whereIn('estado', ['pendiente', 'en_revision'])->count();
        $pagosVencidos    = $pagos->filter(fn($p) => method_exists($p, 'estaVencido') && $p->estaVencido())->count();

        // Panel: próximo pago pendiente (el de vencimiento más cercano)
        $proximoPago = $pagos->where('estado', 'pendiente')
            ->sortBy('fecha_vencimiento')
            ->first();

        // Panel: documentos por actualizar (vencidos, por vencer o faltantes)
        $catalogoDocumentos = DocumentoAlumno::catalogoPara($alumno->programa->nivel ?? 'licenciatura');
        $documentosPorTipo  = $alumno->documentos->keyBy('tipo');

        $itemsDocumentos = collect($catalogoDocumentos)->map(function ($item) use ($documentosPorTipo) {
            $item['documento'] = $documentosPorTipo->get($item['tipo']);
            return $item;
        });

        $documentosFaltantes = $itemsDocumentos->filter(fn($i) => !$i['documento']);
        $documentosVencidos  = $itemsDocumentos->filter(fn($i) => $i['documento']?->estaVencido());
        $documentosPorVencer = $itemsDocumentos->filter(fn($i) => $i['documento']?->porVencer());
        $documentosAtencion  = $documentosVencidos->concat($documentosPorVencer)->concat($documentosFaltantes)->take(5)->values();

        $plazoMigradoDocs = ($alumno->migrado && $documentosFaltantes->isNotEmpty())
            ? $alumno->created_at->copy()->addMonth()
            : null;

        return view('alumno.dashboard', compact(
            'alumno', 'carga', 'totalCreditos', 'pagos',
            'promedioGeneral', 'pagosPendientes', 'pagosVencidos',
            'calificacionesRecientes', 'proximoPago',
            'documentosAtencion', 'documentosFaltantes', 'plazoMigradoDocs'
        ));
    }

    public function materias()
    {
        $alumno = Alumno::where('user_id', Auth::id())
            ->with(['programa', 'grupo.periodo'])
            ->firstOrFail();

        $carga = $alumno->grupo_id
            ? CargaAcademica::with(['materia', 'profesor'])
                ->where('grupo_id', $alumno->grupo_id)
                ->get()
                ->sortBy(fn ($c) => $c->materia->nombre)
                ->values()
            : collect();

        $totalCreditos = $carga->sum(fn ($c) => $c->materia->creditos ?? 0);

        return view('alumno.materias.index', compact('alumno', 'carga', 'totalCreditos'));
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
        return $this->kardexData('alumno.kardex');
    }

    public function kardexImprimir()
    {
        return $this->kardexData('alumno.kardex-imprimir');
    }

    private function kardexData(string $view): \Illuminate\View\View
    {
        $alumno = Alumno::where('user_id', Auth::id())
            ->with(['programa', 'grupo.periodo', 'periodo'])
            ->firstOrFail();

        $cargasActuales = $alumno->grupo_id
            ? CargaAcademica::where('grupo_id', $alumno->grupo_id)
                ->with(['materia', 'profesor', 'periodo'])
                ->get()
            : collect();

        $calificaciones = Calificacion::where('alumno_id', $alumno->id)
            ->with('cargaAcademica.materia', 'cargaAcademica.profesor', 'cargaAcademica.periodo')
            ->get()
            ->filter(fn($c) => $c->cargaAcademica?->estado_revision === 'aprobado')
            ->groupBy('carga_academica_id');

        $cargasHistoricas = $calificaciones->map(fn($grupo) => $grupo->first()->cargaAcademica)->filter();
        $cargas = $cargasActuales->concat($cargasHistoricas)->unique('id');

        $materias = $cargas->map(function ($c) use ($calificaciones) {
            $calif    = $calificaciones->get($c->id, collect());
            $final    = $calif->firstWhere('tipo', 'final');
            $ex       = $calif->firstWhere('tipo', 'extraordinario');
            $calFinal = $ex ? $ex->calificacion : $final?->calificacion;
            return [
                'carga'    => $c,
                'final'    => $final,
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

        return view($view, compact(
            'alumno', 'porCuatrimestre', 'promedioGeneral',
            'aprobadas', 'reprobadas', 'pendientes', 'totalCreditosPrograma'
        ));
    }

    public function comprobante(Pago $pago)
    {
        $alumno = Alumno::where('user_id', Auth::id())->firstOrFail();

        $esSuyo = ($pago->alumno_id === $alumno->id)
               || ($pago->aspirante_id !== null && $pago->aspirante_id === $alumno->aspirante_id);

        abort_if(!$esSuyo, 403);

        return view('alumno.comprobante', compact('pago', 'alumno'));
    }
}
