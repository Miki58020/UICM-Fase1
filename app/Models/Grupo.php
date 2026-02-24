<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $fillable = ['clave', 'programa_id', 'ciclo', 'cuatrimestre', 'capacidad'];

    public function programa()
    {
        return $this->belongsTo(Programa::class);
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
