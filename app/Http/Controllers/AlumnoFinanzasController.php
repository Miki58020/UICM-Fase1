<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;

class AlumnoFinanzasController extends Controller
{
    public function index()
    {
        $alumno = Alumno::where('user_id', Auth::id())->with('programa')->firstOrFail();

        $pagos = $alumno->todosLosPagos()->sortByDesc('created_at');

        $pendientes = $pagos->where('estado', 'pendiente');
        $historial  = $pagos->whereIn('estado', ['aprobado', 'rechazado']);

        // Un pago ya enviado a Mercado Pago (con mp_payment_id) está en revisión, no "sin tocar":
        // no debe contar como atrasado ni invitarse a pagar de nuevo mientras se resuelve.
        $pendientesSinEnviar = $pendientes->filter(fn (Pago $p) => !$p->mp_payment_id);

        $atrasados   = $pendientesSinEnviar->filter(fn (Pago $p) => $p->estaVencido());
        $alCorriente = $atrasados->isEmpty();

        $porVencerPronto = $pendientesSinEnviar->filter(fn (Pago $p) => !$p->estaVencido()
            && $p->fecha_vencimiento
            && $p->fecha_vencimiento->lte(now()->addDays(7)));

        // KPIs de resumen
        $totalPagado        = $historial->where('estado', 'aprobado')->sum('monto');
        $montoPendiente     = $pendientes->sum('monto');
        $proximoVencimiento = $pendientes->sortBy('fecha_vencimiento')->first()?->fecha_vencimiento;

        return view('alumno.finanzas.index', compact(
            'alumno', 'pendientes', 'historial', 'atrasados', 'alCorriente', 'porVencerPronto',
            'totalPagado', 'montoPendiente', 'proximoVencimiento'
        ));
    }
}
