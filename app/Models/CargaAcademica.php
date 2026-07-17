<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CargaAcademica extends Model
{
    protected $table = 'carga_academica';

    protected $fillable = [
        'grupo_id', 'materia_id', 'profesor_id', 'dia_semana', 'hora_inicio', 'hora_fin', 'aula', 'periodo_id',
        'fecha_inicio', 'fecha_fin', 'estado_revision', 'motivo_rechazo', 'revisado_por', 'revisado_at',
    ];

    public const DIAS_SEMANA = [
        'lunes'      => 'Lunes',
        'martes'     => 'Martes',
        'miercoles'  => 'Miércoles',
        'jueves'     => 'Jueves',
        'viernes'    => 'Viernes',
        'sabado'     => 'Sábados',
        'domingo'    => 'Domingos',
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

    public function getHorarioFormateadoAttribute(): ?string
    {
        if (! $this->dia_semana) {
            return null;
        }

        $dia = self::DIAS_SEMANA[$this->dia_semana] ?? ucfirst($this->dia_semana);

        if ($this->hora_inicio && $this->hora_fin) {
            return "{$dia} {$this->hora_inicio} - {$this->hora_fin}";
        }

        return $dia;
    }

    public function dentroDeVentana(): bool
    {
        if (! $this->fecha_inicio || ! $this->fecha_fin) {
            return true;
        }

        $hoy = now()->startOfDay();

        return $hoy->gte($this->fecha_inicio) && $hoy->lte($this->fecha_fin);
    }

    // Una vez enviadas a revisión (pendiente) o aprobadas, el profesor ya no puede
    // seguir editando: pendiente espera el fallo de Coordinación, y aprobado solo
    // se corrige a través de una aclaración formal.
    public function puedeCapturar(): bool
    {
        return match ($this->estado_revision) {
            'aprobado', 'pendiente' => false,
            'rechazado' => true,
            default => $this->dentroDeVentana(),
        };
    }
}
