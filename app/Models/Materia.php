<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = ['clave', 'nombre', 'creditos', 'cuatrimestre', 'programa_id', 'activo'];

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function cargaAcademica()
    {
        return $this->hasMany(CargaAcademica::class);
    }
}
