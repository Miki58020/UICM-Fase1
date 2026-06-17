<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    protected $fillable = ['clave', 'nombre', 'nivel', 'duracion_cuatrimestres', 'total_creditos', 'activo', 'numero_carrera'];

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

    public function periodos()
    {
        return $this->belongsToMany(Periodo::class, 'periodo_programa')
            ->withPivot('numero_carrera', 'numero_generacion', 'activo')
            ->withTimestamps();
    }
}
