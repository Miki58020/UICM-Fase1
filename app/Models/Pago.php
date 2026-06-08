<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'aspirante_id', 'alumno_id', 'concepto', 'periodo',
        'monto', 'descuento', 'monto_original', 'comprobante', 'fecha_pago', 'estado', 'observaciones',
        'mp_preference_id', 'mp_payment_id',
    ];

    protected $casts = [
        'fecha_pago'     => 'date',
        'monto'          => 'decimal:2',
        'descuento'      => 'decimal:2',
        'monto_original' => 'decimal:2',
    ];

    public function aspirante()
    {
        return $this->belongsTo(Aspirante::class);
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}
