<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifaInscripcion extends Model
{
    protected $table    = 'tarifas_inscripcion';
    protected $fillable = ['nivel', 'tipo', 'monto', 'descuento', 'descuento_fecha_inicio', 'descuento_fecha_fin'];
    protected $casts    = [
        'monto'                  => 'float',
        'descuento'              => 'float',
        'descuento_fecha_inicio' => 'date:Y-m-d',
        'descuento_fecha_fin'    => 'date:Y-m-d',
    ];

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

    public function getPrecioFinalAttribute(): float
    {
        if (!$this->descuentoVigente()) {
            return $this->monto;
        }

        return round($this->monto * (1 - $this->descuento / 100), 2);
    }
}
