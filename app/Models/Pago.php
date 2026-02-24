<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'aspirante_id', 'alumno_id', 'concepto', 'periodo',
        'monto', 'comprobante', 'fecha_pago', 'estado', 'observaciones',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto'      => 'decimal:2',
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
