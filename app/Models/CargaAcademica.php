<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CargaAcademica extends Model
{
    protected $table = 'carga_academica';

    protected $fillable = ['grupo_id', 'materia_id', 'profesor_id', 'horario', 'aula', 'ciclo'];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }
}
