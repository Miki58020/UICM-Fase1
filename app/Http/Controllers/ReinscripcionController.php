<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\TarifaInscripcion;
use Illuminate\Http\Request;

class ReinscripcionController extends Controller
{
    public function index()
    {
        $periodoActivo = Periodo::where('estado', 'activo')->first();

        $alumnos = Alumno::where('estado', 'activo')
            ->with(['programa', 'grupo.periodo'])
            ->when($periodoActivo, fn($q) => $q->with([
                'reinscripciones' => fn($q) => $q
                    ->where('concepto', 'reinscripcion')
                    ->where('periodo', $periodoActivo->nombre),
            ]))
            ->orderBy('apellido_paterno')
            ->orderBy('nombre')
            ->get();

        $gruposActivos = $periodoActivo
            ? Grupo::where('periodo_id', $periodoActivo->id)->orderBy('clave')->get()
            : collect();

        return view('admin.reinscripciones.index', compact('alumnos', 'periodoActivo', 'gruposActivos'));
    }

    public function generarPago(Alumno $alumno)
    {
        $periodoActivo = Periodo::where('estado', 'activo')->firstOrFail();

        $existente = Pago::where('alumno_id', $alumno->id)
            ->where('concepto', 'reinscripcion')
            ->where('periodo', $periodoActivo->nombre)
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->first();

        if ($existente) {
            return redirect()->route('admin.reinscripciones.index')
                ->with('error', 'Este alumno ya tiene un cobro de reinscripción activo para este período.');
        }

        $tarifa = TarifaInscripcion::where('nivel', $alumno->programa?->nivel)
            ->where('tipo', 'cuatrimestre')
            ->first();

        $monto = $tarifa?->precio_final ?? match ($alumno->programa?->nivel) {
            'maestria'  => 4000.00,
            'doctorado' => 5000.00,
            default     => 3000.00,
        };

        Pago::create([
            'alumno_id'      => $alumno->id,
            'concepto'       => 'reinscripcion',
            'periodo'        => $periodoActivo->nombre,
            'monto'          => $monto,
            'descuento'      => $tarifa && $tarifa->descuentoVigente() ? $tarifa->descuento : 0,
            'monto_original' => $tarifa?->monto,
            'fecha_vencimiento' => $periodoActivo->fecha_fin_registro,
            'estado'         => 'pendiente',
        ]);

        return redirect()->route('admin.reinscripciones.index')
            ->with('success', "Cobro de reinscripción generado para {$alumno->nombre_completo}.");
    }

    public function completar(Request $request, Alumno $alumno)
    {
        $periodoActivo = Periodo::where('estado', 'activo')->firstOrFail();

        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
        ]);

        Pago::where('alumno_id', $alumno->id)
            ->where('concepto', 'reinscripcion')
            ->where('periodo', $periodoActivo->nombre)
            ->where('estado', 'aprobado')
            ->firstOrFail();

        $grupo = Grupo::findOrFail($request->grupo_id);

        $nuevoCuatrimestre = $alumno->cuatrimestre_actual + 1;

        $alumno->update([
            'grupo_id'            => $grupo->id,
            'cuatrimestre_actual' => $nuevoCuatrimestre,
        ]);

        return redirect()->route('admin.reinscripciones.index')
            ->with('success', "Reinscripción completada para {$alumno->nombre_completo}. Grupo {$grupo->clave}, cuatrimestre {$nuevoCuatrimestre}.");
    }
}
