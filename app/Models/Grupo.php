<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $fillable = ['clave', 'programa_id', 'periodo_id', 'cuatrimestre', 'capacidad'];

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }

    public function cargaAcademica()
    {
        return $this->hasMany(CargaAcademica::class);
    }
}
