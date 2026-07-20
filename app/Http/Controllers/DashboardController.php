<?php

namespace App\Http\Controllers;

use App\Models\AclaracionCalificacion;
use App\Models\Alumno;
use App\Models\Aspirante;
use App\Models\CargaAcademica;
use App\Models\Contacto;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\Profesor;
use App\Models\SolicitudContrasena;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $rol = Auth::user()->rol;

        if ($rol === 'alumno') return redirect()->route('alumno.dashboard');

        $data = match ($rol) {
            'control_escolar' => $this->datosControlEscolar(),
            'finanzas'        => $this->datosFinanzas(),
            'coordinacion'    => $this->datosCoordinacion(),
            'profesor'        => $this->datosProfesor(),
            default           => $this->datosAdmin(),
        };

        return view('dashboard', array_merge(['rol' => $rol], $data));
    }

    private function datosProfesor(): array
    {
        $profesor = Profesor::where('user_id', Auth::id())->first();

        $cargas = $profesor
            ? CargaAcademica::where('profesor_id', $profesor->id)
                ->with(['materia', 'grupo.alumnos', 'periodo'])
                ->orderByDesc('periodo_id')
                ->get()
            : collect();

        // "Por atender": el profesor todavía no ha enviado la materia a revisión (nunca o tras un rechazo).
        // No incluye 'pendiente' (ya está en revisión de control escolar, no requiere acción del profesor).
        $requiereAccion = fn ($c) => in_array($c->estado_revision, [null, 'rechazado'], true);

        return [
            'materiasProfesor'       => $cargas->count(),
            'gruposProfesor'         => $cargas->pluck('grupo_id')->unique()->count(),
            'alumnosProfesor'        => $cargas->sum(fn($c) => $c->grupo?->alumnos->where('estado', 'activo')->count() ?? 0),
            'porCalificarProfesor'   => $cargas->filter($requiereAccion)->count(),
            'gruposPorAtender'       => $cargas->filter($requiereAccion)
                ->sortBy(fn($c) => $c->fecha_fin ?? now()->addYears(10))
                ->take(5)
                ->values(),
            'aclaracionesPendientes' => $profesor
                ? AclaracionCalificacion::where('profesor_id', $profesor->id)->where('estado', 'pendiente')->count()
                : 0,
        ];
    }

    private function datosControlEscolar(): array
    {
        return [
            'aspirantesPendientes'  => Aspirante::where('estado', 'pendiente')->count(),
            'listosInscribir'       => Aspirante::where('estado', 'aprobado')
                ->whereHas('pagos', fn($q) => $q->where('estado', 'aprobado'))
                ->whereHas('alumno', fn($q) => $q->whereNull('user_id'))
                ->count(),
            'alumnosActivos'        => Alumno::where('estado', 'activo')->count(),
            'solicitudesContrasena' => SolicitudContrasena::where('estado', 'pendiente')
                ->whereHas('user', fn($u) => $u->where('rol', 'alumno'))
                ->count(),
            'ultimosAspirantes'     => Aspirante::with('programa')->latest()->take(6)->get(),
        ];
    }

    private function datosFinanzas(): array
    {
        return [
            'pagosPendientes' => Pago::conIntentoDePago()->where('estado', 'pendiente')->count(),
            'pagosAprobados'  => Pago::where('estado', 'aprobado')->count(),
            'pagosRechazados' => Pago::where('estado', 'rechazado')->count(),
            'montoTotal'      => (int) Pago::where('estado', 'aprobado')->sum('monto'),
            'ultimosPagos'    => Pago::conIntentoDePago()->with('aspirante')->latest()->take(6)->get(),
        ];
    }

    private function datosCoordinacion(): array
    {
        $periodo = Periodo::where('activo', true)->first();

        return [
            'periodoActivo'          => $periodo,
            'totalGrupos'            => Grupo::count(),
            'totalProfesores'        => Profesor::where('activo', true)->count(),
            'totalMaterias'          => Materia::where('activo', true)->count(),
            'gruposSinCarga'         => Grupo::whereDoesntHave('cargaAcademica')->count(),
            'ultimosGrupos'          => Grupo::with(['programa', 'periodo'])->latest()->take(5)->get(),
            'totalPeriodos'          => Periodo::count(),
            'gruposPorCuatrimestre'  => Grupo::selectRaw('cuatrimestre, count(*) as total')
                                            ->groupBy('cuatrimestre')
                                            ->orderBy('cuatrimestre')
                                            ->pluck('total', 'cuatrimestre'),
        ];
    }

    private function datosAdmin(): array
    {
        // Control Escolar
        $ceAspirantes   = Aspirante::where('estado', 'pendiente')->count();
        $ceInscripciones = Aspirante::where('estado', 'aprobado')
            ->whereHas('pagos', fn($q) => $q->where('estado', 'aprobado'))
            ->whereHas('alumno', fn($q) => $q->whereNull('user_id'))
            ->count();
        $ceContrasenas  = SolicitudContrasena::where('estado', 'pendiente')
            ->whereHas('user', fn($u) => $u->where('rol', 'alumno'))
            ->count();

        // Finanzas
        $finPagos = Pago::conIntentoDePago()->where('estado', 'pendiente')->count();

        // Coordinación
        $coordSinCarga    = Grupo::whereDoesntHave('cargaAcademica')->count();
        $coordContrasenas = SolicitudContrasena::where('estado', 'pendiente')
            ->whereHas('user', fn($u) => $u->where('rol', 'profesor'))
            ->count();

        // Admin
        $adminMensajes    = Contacto::where('atendido', false)->count();
        $adminContrasenas = SolicitudContrasena::where('estado', 'pendiente')
            ->whereHas('user', fn($u) => $u->whereNotIn('rol', ['alumno', 'profesor']))
            ->count();

        return [
            'totalUsuariosSistema'  => User::whereIn('rol', UsuarioController::ROLES)->count(),
            'aspirantesEnProceso'   => $ceAspirantes + $ceInscripciones,
            'mensajesPendientes'    => $adminMensajes,
            'solicitudesPendientes' => SolicitudContrasena::where('estado', 'pendiente')->count(),
            'deptos' => [
                [
                    'rolKey' => 'control_escolar',
                    'label'  => 'Control Escolar',
                    'total'  => $ceAspirantes + $ceInscripciones + $ceContrasenas,
                    'items'  => [
                        ['label' => 'Aspirantes por validar', 'count' => $ceAspirantes],
                        ['label' => 'Listos para inscribir',  'count' => $ceInscripciones],
                        ['label' => 'Contraseñas alumnos',    'count' => $ceContrasenas],
                    ],
                    'href'   => route('admin.aspirantes.index'),
                    'color'  => '#D4AF37',
                    'bgIcon' => '#fdf8ec',
                ],
                [
                    'rolKey' => 'finanzas',
                    'label'  => 'Finanzas',
                    'total'  => $finPagos,
                    'items'  => [
                        ['label' => 'Comprobantes por revisar', 'count' => $finPagos],
                    ],
                    'href'   => route('finanzas.pagos.index'),
                    'color'  => '#0F4229',
                    'bgIcon' => '#f0f9f4',
                ],
                [
                    'rolKey' => 'coordinacion',
                    'label'  => 'Coordinación',
                    'total'  => $coordSinCarga + $coordContrasenas,
                    'items'  => [
                        ['label' => 'Grupos sin carga académica', 'count' => $coordSinCarga],
                        ['label' => 'Contraseñas profesores',     'count' => $coordContrasenas],
                    ],
                    'href'   => route('admin.carga-academica.index'),
                    'color'  => '#EFAD5A',
                    'bgIcon' => '#fef4e8',
                ],
                [
                    'rolKey' => 'admin',
                    'label'  => 'Administración',
                    'total'  => $adminMensajes + $adminContrasenas,
                    'items'  => [
                        ['label' => 'Mensajes de contacto', 'count' => $adminMensajes],
                        ['label' => 'Contraseñas sistema',  'count' => $adminContrasenas],
                    ],
                    'href'   => route('admin.contactos.index'),
                    'color'  => '#0F4229',
                    'bgIcon' => '#f0f9f4',
                ],
            ],
            'usuariosPorRol'  => User::selectRaw('rol, count(*) as total')
                ->whereNotIn('rol', ['alumno', 'profesor'])
                ->groupBy('rol')
                ->pluck('total', 'rol'),
            'ultimosUsuarios' => User::whereNotIn('rol', ['alumno', 'profesor'])->latest()->take(6)->get(),
        ];
    }
}
