<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    protected $fillable = ['alumno_id', 'carga_academica_id', 'tipo', 'numero', 'calificacion'];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function cargaAcademica()
    {
        return $this->belongsTo(CargaAcademica::class);
    }

    public function aprobado(): bool
    {
        return $this->calificacion >= 7.0;
    }
}
