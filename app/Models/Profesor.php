<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    protected $fillable = ['nombre', 'correo', 'telefono', 'especialidad', 'activo'];

    public function cargaAcademica()
    {
        return $this->hasMany(CargaAcademica::class);
    }
}
