<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifaInscripcion extends Model
{
    protected $table    = 'tarifas_inscripcion';
    protected $fillable = ['nivel', 'tipo', 'monto', 'descuento'];
    protected $casts    = ['monto' => 'float', 'descuento' => 'float'];

    public function getPrecioFinalAttribute(): float
    {
        return round($this->monto * (1 - $this->descuento / 100), 2);
    }
}
