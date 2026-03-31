<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $fillable = ['nombre', 'label', 'fecha_inicio', 'fecha_fin', 'estado'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
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
