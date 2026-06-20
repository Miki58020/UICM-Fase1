<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AclaracionCalificacion extends Model
{
    protected $table = 'aclaraciones_calificaciones';

    protected $fillable = [
        'alumno_id', 'carga_academica_id', 'profesor_id', 'tipo',
        'calificacion_propuesta', 'motivo', 'estado',
        'revisado_por', 'revisado_at', 'motivo_rechazo',
    ];

    protected $casts = [
        'revisado_at' => 'datetime',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function cargaAcademica()
    {
        return $this->belongsTo(CargaAcademica::class);
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }

    public function revisadoPor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }
}
