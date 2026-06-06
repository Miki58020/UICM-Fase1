<?php

namespace App\Http\Controllers;

use App\Mail\RecordatorioManual;
use App\Models\Aspirante;
use App\Models\Contacto;
use App\Models\Grupo;
use App\Models\Pago;
use App\Models\SolicitudContrasena;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificacionDepartamentoController extends Controller
{
    private const NOMBRES = [
        'control_escolar' => 'Control Escolar',
        'finanzas'        => 'Finanzas',
        'coordinacion'    => 'Coordinación',
        'admin'           => 'Administración',
    ];

    public function enviar(string $rol)
    {
        if (!array_key_exists($rol, self::NOMBRES)) {
            return redirect()->route('dashboard')->with('notif_error', 'Departamento no válido.');
        }

        $datos    = $this->datosPara($rol);
        $usuarios = User::where('rol', $rol)->get();
        $enviados = 0;
        $fallidos = 0;

        foreach ($usuarios as $usuario) {
            try {
                Mail::to($usuario->email)->send(new RecordatorioManual($usuario, $datos));
                $enviados++;
            } catch (\Exception) {
                $fallidos++;
            }
        }

        $nombre = self::NOMBRES[$rol];

        if ($enviados === 0 && $fallidos === 0) {
            return redirect()->route('dashboard')->with('notif_warning_'.$rol, "No hay usuarios registrados en {$nombre}.");
        }

        if ($fallidos > 0) {
            return redirect()->route('dashboard')->with('notif_warning_'.$rol, "Se enviaron {$enviados} notificación(es) a {$nombre}; {$fallidos} fallaron.");
        }

        return redirect()->route('dashboard')->with('notif_ok_'.$rol, "Notificación enviada a {$enviados} usuario(s) de {$nombre}.");
    }

    private function datosPara(string $rol): array
    {
        return match ($rol) {
            'control_escolar' => [
                'Aspirantes pendientes de revisión'     => Aspirante::where('estado', 'pendiente')->count(),
                'Listos para inscribir (pago aprobado)' => Aspirante::where('estado', 'aprobado')
                    ->whereHas('pagos', fn($q) => $q->where('estado', 'aprobado'))
                    ->whereHas('alumno', fn($q) => $q->whereNull('user_id'))
                    ->count(),
                'Solicitudes de contraseña pendientes'  => SolicitudContrasena::where('estado', 'pendiente')
                    ->whereHas('user', fn($u) => $u->where('rol', 'alumno'))
                    ->count(),
            ],
            'finanzas' => [
                'Comprobantes de pago por revisar' => Pago::where('estado', 'pendiente')->count(),
                'Pagos aprobados (acumulado)'      => Pago::where('estado', 'aprobado')->count(),
            ],
            'coordinacion' => [
                'Grupos sin carga académica asignada' => Grupo::whereDoesntHave('cargaAcademica')->count(),
                'Solicitudes de contraseña (profes)'  => SolicitudContrasena::where('estado', 'pendiente')
                    ->whereHas('user', fn($u) => $u->where('rol', 'profesor'))
                    ->count(),
            ],
            'admin' => [
                'Aspirantes pendientes de revisión'    => Aspirante::where('estado', 'pendiente')->count(),
                'Comprobantes de pago por validar'     => Pago::where('estado', 'pendiente')->count(),
                'Mensajes de contacto sin atender'     => Contacto::where('atendido', false)->count(),
                'Solicitudes de contraseña pendientes' => SolicitudContrasena::where('estado', 'pendiente')->count(),
            ],
        };
    }
}
