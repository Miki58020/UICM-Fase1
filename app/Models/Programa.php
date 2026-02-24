<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    protected $fillable = ['clave', 'nombre', 'nivel', 'duracion_cuatrimestres', 'activo'];

    public function aspirantes()
    {
        return $this->hasMany(Aspirante::class);
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }

    public function materias()
    {
        return $this->hasMany(Materia::class);
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
