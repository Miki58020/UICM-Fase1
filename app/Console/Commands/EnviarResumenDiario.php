<?php

namespace App\Console\Commands;

use App\Mail\ResumenDiario;
use App\Models\Alumno;
use App\Models\Aspirante;
use App\Models\Pago;
use App\Models\SolicitudContrasena;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarResumenDiario extends Command
{
    protected $signature   = 'uicm:resumen-diario';
    protected $description = 'Envía el resumen diario de actividad a cada usuario del sistema según su rol';

    public function handle(): void
    {
        $this->enviarControlEscolar();
        $this->enviarFinanzas();
        $this->enviarCoordinacion();
        $this->enviarAdmin();

        $this->info('Resúmenes diarios enviados correctamente.');
    }

    // ── Control Escolar ───────────────────────────────────────────────────────

    private function enviarControlEscolar(): void
    {
        $aspirantesPendientes = Aspirante::where('estado', 'pendiente')->count();

        $listosParaInscribir = Aspirante::where('estado', 'aprobado')
            ->whereHas('pagos', fn($q) => $q->where('estado', 'aprobado'))
            ->whereHas('alumno', fn($q) => $q->whereNull('user_id'))
            ->count();

        $solicitudesContrasena = SolicitudContrasena::where('estado', 'pendiente')
            ->whereHas('user', fn($q) => $q->where('rol', 'alumno'))
            ->count();

        $datos = [
            'Aspirantes pendientes de revisión'          => $aspirantesPendientes,
            'Listos para inscribir (pago aprobado)'      => $listosParaInscribir,
            'Solicitudes de contraseña de alumnos'        => $solicitudesContrasena,
        ];

        $this->enviarARol('control_escolar', $datos);
    }

    // ── Finanzas ──────────────────────────────────────────────────────────────

    private function enviarFinanzas(): void
    {
        $pagosPendientes = Pago::where('estado', 'pendiente')->count();

        $datos = [
            'Pagos pendientes de validación' => $pagosPendientes,
        ];

        $this->enviarARol('finanzas', $datos);
    }

    // ── Coordinación ──────────────────────────────────────────────────────────

    private function enviarCoordinacion(): void
    {
        $alumnosSinGrupo = Alumno::where('estado', 'activo')
            ->whereNull('grupo_id')
            ->count();

        $solicitudesContrasena = SolicitudContrasena::where('estado', 'pendiente')
            ->whereHas('user', fn($q) => $q->where('rol', 'profesor'))
            ->count();

        $datos = [
            'Alumnos activos sin grupo asignado'        => $alumnosSinGrupo,
            'Solicitudes de contraseña de profesores'   => $solicitudesContrasena,
        ];

        $this->enviarARol('coordinacion', $datos);
    }

    // ── Admin ─────────────────────────────────────────────────────────────────

    private function enviarAdmin(): void
    {
        $datos = [
            'Aspirantes pendientes de revisión'      => Aspirante::where('estado', 'pendiente')->count(),
            'Pagos pendientes de validación'          => Pago::where('estado', 'pendiente')->count(),
            'Listos para inscribir (pago aprobado)'  => Aspirante::where('estado', 'aprobado')
                ->whereHas('pagos', fn($q) => $q->where('estado', 'aprobado'))
                ->whereHas('alumno', fn($q) => $q->whereNull('user_id'))
                ->count(),
            'Alumnos activos sin grupo asignado'     => Alumno::where('estado', 'activo')
                ->whereNull('grupo_id')
                ->count(),
            'Solicitudes de contraseña administrativas' => SolicitudContrasena::where('estado', 'pendiente')
                ->whereHas('user', fn($q) => $q->whereNotIn('rol', ['alumno', 'profesor']))
                ->count(),
        ];

        $this->enviarARol('admin', $datos);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function enviarARol(string $rol, array $datos): void
    {
        $usuarios = User::where('rol', $rol)->get();

        foreach ($usuarios as $usuario) {
            $intentos = 0;

            while ($intentos < 3) {
                try {
                    Mail::to($usuario->email)->send(new ResumenDiario($usuario, $datos));
                    $this->line("  ✓ Enviado a {$usuario->email}");
                    sleep(3);
                    break;
                } catch (\Exception $e) {
                    $intentos++;
                    $this->warn("  Intento {$intentos} fallido para {$usuario->email}: {$e->getMessage()}");

                    if ($intentos < 3) {
                        $this->warn("  Esperando 15 segundos antes de reintentar...");
                        sleep(15);
                    } else {
                        $this->error("  No se pudo enviar a {$usuario->email} tras 3 intentos.");
                    }
                }
            }
        }
    }
}
