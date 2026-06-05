<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    protected $table = 'profesores';

    protected $fillable = ['user_id', 'nombre', 'correo', 'telefono', 'especialidad', 'activo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cargaAcademica()
    {
        return $this->hasMany(CargaAcademica::class);
    }
}
