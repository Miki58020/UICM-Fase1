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

        return view('finanzas.tarifas.index', compact('tarifas', 'periodoActivo'));
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
            'dias_anticipacion_cobro'    => 'nullable|integer|min:1|max:90',
            'dias_para_pagar'            => 'nullable|integer|min:1|max:60',
        ]);

        $descuento        = (float) $request->descuento;
        $fechaIni         = $request->descuento_fecha_inicio;
        $fechaFin         = $request->descuento_fecha_fin;
        $diaLimite        = null;
        $diasDescuento    = null;
        $diasAnticipacion = null;
        $diasParaPagar    = null;

        if ($tarifa->tipo === 'inscripcion') {
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
        } elseif ($tarifa->tipo === 'cuatrimestre') {
            $periodoActivo = Periodo::activo();

            if (!$periodoActivo || !$periodoActivo->fecha_fin_clases) {
                return redirect()->back()->withErrors([
                    'dias_para_pagar' => 'Para configurar esta tarifa primero debe definirse la fecha de fin de clases del cuatrimestre activo.',
                ])->withInput();
            }

            $diasAnticipacion = $request->dias_anticipacion_cobro;
            $diasParaPagar    = $request->dias_para_pagar;

            if (!$diasAnticipacion) {
                return redirect()->back()->withErrors([
                    'dias_anticipacion_cobro' => 'Indica con cuántos días de anticipación a fin de clases se generará el cobro.',
                ])->withInput();
            }

            if (!$diasParaPagar) {
                return redirect()->back()->withErrors([
                    'dias_para_pagar' => 'Indica cuántos días tendrá el alumno para pagar a partir de que se genere el cobro.',
                ])->withInput();
            }

            $apertura    = $periodoActivo->fecha_fin_clases->copy()->subDays((int) $diasAnticipacion);
            $vencimiento = $apertura->copy()->addDays((int) $diasParaPagar);

            if ($descuento > 0) {
                if (!$fechaIni || !$fechaFin) {
                    return redirect()->back()->withErrors([
                        'descuento' => 'Debes indicar el rango de fechas en el que aplicará el descuento.',
                    ])->withInput();
                }

                if ($fechaIni < $apertura->toDateString() || $fechaFin > $vencimiento->toDateString()) {
                    return redirect()->back()->withErrors([
                        'descuento' => 'El rango del descuento debe estar dentro de la ventana de pago del cuatrimestre ('
                            . $apertura->format('d/m/Y') . ' - ' . $vencimiento->format('d/m/Y') . ').',
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
            'dias_anticipacion_cobro'    => $diasAnticipacion,
            'dias_para_pagar'            => $diasParaPagar,
        ]);

        return redirect()->back()->with('success', 'Tarifa actualizada correctamente.');
    }
}
