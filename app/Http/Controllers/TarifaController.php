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
            'monto'                      => 'required|numeric|min:1|max:99999.99',
            'descuento'                  => 'required|numeric|min:0|max:100',
            'descuento_fecha_inicio'     => 'nullable|date',
            'descuento_fecha_fin'        => 'nullable|date|after_or_equal:descuento_fecha_inicio',
            'dia_limite_pago'            => 'nullable|integer|min:1|max:28',
            'dias_descuento_pronto_pago' => 'nullable|integer|min:1|max:28',
        ]);

        $descuento     = (float) $request->descuento;
        $fechaIni      = $request->descuento_fecha_inicio;
        $fechaFin      = $request->descuento_fecha_fin;
        $diaLimite     = null;
        $diasDescuento = null;

        if (in_array($tarifa->tipo, ['inscripcion', 'cuatrimestre'])) {
            if ($descuento > 0) {
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
        } elseif ($tarifa->tipo === 'colegiatura') {
            $fechaIni = null;
            $fechaFin = null;
            $diaLimite = $request->dia_limite_pago;
            $diasDescuento = $request->dias_descuento_pronto_pago;

            if (!$diaLimite) {
                return redirect()->back()->withErrors([
                    'dia_limite_pago' => 'Indica el día del mes límite de pago antes de considerarse atrasado.',
                ])->withInput();
            }

            if ($descuento > 0 && !$diasDescuento) {
                return redirect()->back()->withErrors([
                    'dias_descuento_pronto_pago' => 'Indica cuántos primeros días del mes tendrán el descuento por pronto pago.',
                ])->withInput();
            }

            if ($diasDescuento && $diasDescuento >= $diaLimite) {
                return redirect()->back()->withErrors([
                    'dias_descuento_pronto_pago' => 'Los días de descuento deben ser menores al día límite de pago.',
                ])->withInput();
            }
        } else {
            $fechaIni = null;
            $fechaFin = null;
        }

        $tarifa->update([
            'monto'                      => $request->monto,
            'descuento'                  => $descuento,
            'descuento_fecha_inicio'     => $fechaIni,
            'descuento_fecha_fin'        => $fechaFin,
            'dia_limite_pago'            => $diaLimite,
            'dias_descuento_pronto_pago' => $diasDescuento,
        ]);

        return redirect()->back()->with('success', 'Tarifa actualizada correctamente.');
    }
}
