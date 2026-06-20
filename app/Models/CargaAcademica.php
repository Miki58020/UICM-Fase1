<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CargaAcademica extends Model
{
    protected $table = 'carga_academica';

    protected $fillable = [
        'grupo_id', 'materia_id', 'profesor_id', 'horario', 'aula', 'periodo_id',
        'fecha_inicio', 'fecha_fin', 'estado_revision', 'motivo_rechazo', 'revisado_por', 'revisado_at',
    ];

    protected $casts = [
        'fecha_inicio' => 'date:Y-m-d',
        'fecha_fin' => 'date:Y-m-d',
        'revisado_at' => 'datetime',
    ];

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

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }

    public function aclaraciones()
    {
        return $this->hasMany(AclaracionCalificacion::class);
    }

    public function revisadoPor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function dentroDeVentana(): bool
    {
        if (! $this->fecha_inicio || ! $this->fecha_fin) {
            return true;
        }

        $hoy = now()->startOfDay();

        return $hoy->gte($this->fecha_inicio) && $hoy->lte($this->fecha_fin);
    }
}
