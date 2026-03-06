<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspirante extends Model
{
    protected $fillable = [
        'folio', 'nombre', 'apellido_paterno', 'apellido_materno',
        'email', 'telefono', 'curp', 'fecha_nacimiento', 'programa_id',
        'generacion', 'acta_nacimiento_url', 'certificado_url', 'identificacion_url',
        'curp_url', 'titulo_url',
        'estado', 'observaciones',
    ];

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function alumno()
    {
        return $this->hasOne(Alumno::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }
}
