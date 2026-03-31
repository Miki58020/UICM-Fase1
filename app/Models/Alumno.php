<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $fillable = [
        'matricula', 'user_id', 'aspirante_id', 'programa_id', 'grupo_id',
        'nombre', 'apellido_paterno', 'apellido_materno', 'email',
        'cuatrimestre_actual', 'creditos_acumulados', 'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aspirante()
    {
        return $this->belongsTo(Aspirante::class);
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'aspirante_id', 'aspirante_id');
    }

    public function reinscripciones()
    {
        return $this->hasMany(Pago::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }
}
