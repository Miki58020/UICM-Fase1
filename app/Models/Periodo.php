<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $fillable = [
        'nombre', 'label', 'estado', 'auto',
        'fecha_inicio_registro', 'fecha_fin_registro',
        'fecha_inicio_clases',   'fecha_fin_clases',
    ];

    protected $casts = [
        'auto'                  => 'boolean',
        'fecha_inicio_registro' => 'date',
        'fecha_fin_registro'    => 'date',
        'fecha_inicio_clases'   => 'date',
        'fecha_fin_clases'      => 'date',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    public function cargaAcademica()
    {
        return $this->hasMany(CargaAcademica::class);
    }

    public static function activo()
    {
        return static::where('estado', 'activo')->first();
    }
}
