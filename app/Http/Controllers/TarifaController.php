<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use App\Models\TarifaInscripcion;
use Illuminate\Http\Request;

class TarifaController extends Controller
{
    public function index()
    {
        $tarifas = TarifaInscripcion::orderByRaw("
            CASE nivel
                WHEN 'licenciatura' THEN 1
                WHEN 'maestria'     THEN 2
                WHEN 'doctorado'    THEN 3
                ELSE 4
            END
        ")->get()->groupBy('tipo');

        $periodoActivo = Periodo::activo();

        return view('admin.tarifas.index', compact('tarifas', 'periodoActivo'));
    }

    public function update(Request $request, TarifaInscripcion $tarifa)
    {
        $request->validate([
            'monto'                  => 'required|numeric|min:1|max:99999.99',
            'descuento'              => 'required|numeric|min:0|max:100',
            'descuento_fecha_inicio' => 'nullable|date',
            'descuento_fecha_fin'    => 'nullable|date|after_or_equal:descuento_fecha_inicio',
        ]);

        $descuento  = (float) $request->descuento;
        $fechaIni   = $request->descuento_fecha_inicio;
        $fechaFin   = $request->descuento_fecha_fin;

        if ($descuento > 0 && $tarifa->tipo === 'inscripcion') {
            $periodoActivo = Periodo::activo();

            if (!$periodoActivo || !$periodoActivo->fecha_inicio_registro || !$periodoActivo->fecha_fin_registro) {
                return redirect()->back()->withErrors([
                    'descuento' => 'Para asignar un descuento primero debe configurarse el rango de inscripción del cuatrimestre activo.',
                ])->withInput();
            }

            if (!$fechaIni || !$fechaFin) {
                return redirect()->back()->withErrors([
                    'descuento' => 'Debes indicar el rango de fechas en el que aplicará el descuento.',
                ])->withInput();
            }

            $inicioRegistro = $periodoActivo->fecha_inicio_registro->toDateString();
            $finRegistro    = $periodoActivo->fecha_fin_registro->toDateString();

            if ($fechaIni < $inicioRegistro || $fechaFin > $finRegistro) {
                return redirect()->back()->withErrors([
                    'descuento' => 'El rango del descuento debe estar dentro del periodo de inscripción del cuatrimestre activo ('
                        . $periodoActivo->fecha_inicio_registro->format('d/m/Y') . ' - '
                        . $periodoActivo->fecha_fin_registro->format('d/m/Y') . ').',
                ])->withInput();
            }
        } else {
            $fechaIni = null;
            $fechaFin = null;
        }

        $tarifa->update([
            'monto'                  => $request->monto,
            'descuento'              => $descuento,
            'descuento_fecha_inicio' => $fechaIni,
            'descuento_fecha_fin'    => $fechaFin,
        ]);

        return redirect()->back()->with('success', 'Tarifa actualizada correctamente.');
    }
}
