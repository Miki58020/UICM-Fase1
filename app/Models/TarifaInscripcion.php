<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifaInscripcion extends Model
{
    protected $table    = 'tarifas_inscripcion';
    protected $fillable = [
        'nivel', 'tipo', 'monto', 'descuento', 'descuento_fecha_inicio', 'descuento_fecha_fin',
        'dia_limite_pago', 'dias_descuento_pronto_pago',
    ];
    protected $casts    = [
        'monto'                  => 'float',
        'descuento'              => 'float',
        'descuento_fecha_inicio' => 'date:Y-m-d',
        'descuento_fecha_fin'    => 'date:Y-m-d',
    ];

    /**
     * Para inscripcion/cuatrimestre: rango de fechas fijo configurado por el admin.
     * Para colegiatura (cargo mensual recurrente) no aplica, ver descuentoVigenteEnFecha().
     */
    public function descuentoVigente(): bool
    {
        if ($this->descuento <= 0) {
            return false;
        }

        if (!$this->descuento_fecha_inicio || !$this->descuento_fecha_fin) {
            return false;
        }

        $hoy = now()->toDateString();

        return $hoy >= $this->descuento_fecha_inicio->toDateString()
            && $hoy <= $this->descuento_fecha_fin->toDateString();
    }

    /**
     * Para colegiatura: el descuento aplica si el pago se hace dentro de los primeros
     * `dias_descuento_pronto_pago` días del mes de la fecha dada.
     */
    public function descuentoVigenteEnFecha(\Carbon\Carbon $fecha): bool
    {
        if ($this->descuento <= 0 || !$this->dias_descuento_pronto_pago) {
            return false;
        }

        return $fecha->day <= $this->dias_descuento_pronto_pago;
    }

    public function getPrecioFinalAttribute(): float
    {
        if (!$this->descuentoVigente()) {
            return $this->monto;
        }

        return round($this->monto * (1 - $this->descuento / 100), 2);
    }

    public function precioParaFecha(\Carbon\Carbon $fecha): float
    {
        if (!$this->descuentoVigenteEnFecha($fecha)) {
            return $this->monto;
        }

        return round($this->monto * (1 - $this->descuento / 100), 2);
    }
}
