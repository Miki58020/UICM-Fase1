<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'aspirante_id', 'alumno_id', 'concepto', 'periodo', 'mes',
        'monto', 'descuento', 'monto_original', 'comprobante', 'fecha_vencimiento', 'fecha_pago', 'estado', 'observaciones',
        'mp_preference_id', 'mp_payment_id',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago'         => 'date',
        'monto'              => 'decimal:2',
        'descuento'          => 'decimal:2',
        'monto_original'     => 'decimal:2',
    ];

    public function aspirante()
    {
        return $this->belongsTo(Aspirante::class);
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function estaVencido(): bool
    {
        return $this->estado === 'pendiente'
            && $this->fecha_vencimiento !== null
            && $this->fecha_vencimiento->isPast();
    }

    /**
     * Genera el cobro de reinscripción de un alumno para un periodo, evitando duplicar
     * si ya tiene uno activo. Usado tanto por el comando automático como por el botón
     * manual de control escolar.
     */
    public static function generarReinscripcion(Alumno $alumno, Periodo $periodo): ?self
    {
        $existente = static::where('alumno_id', $alumno->id)
            ->where('concepto', 'reinscripcion')
            ->where('periodo', $periodo->nombre)
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->first();

        if ($existente) {
            return null;
        }

        $tarifa = TarifaInscripcion::where('nivel', $alumno->programa?->nivel)
            ->where('tipo', 'cuatrimestre')
            ->first();

        $monto = $tarifa?->precio_final ?? match ($alumno->programa?->nivel) {
            'maestria'  => 4000.00,
            'doctorado' => 5000.00,
            default     => 3000.00,
        };

        $fechaVencimiento = $tarifa?->dias_para_pagar
            ? now()->addDays($tarifa->dias_para_pagar)
            : $periodo->fecha_fin_registro;

        return static::create([
            'alumno_id'         => $alumno->id,
            'concepto'          => 'reinscripcion',
            'periodo'           => $periodo->nombre,
            'monto'             => $monto,
            'descuento'         => $tarifa && $tarifa->descuentoVigente() ? $tarifa->descuento : 0,
            'monto_original'    => $tarifa?->monto,
            'fecha_vencimiento' => $fechaVencimiento,
            'estado'            => 'pendiente',
        ]);
    }
}
