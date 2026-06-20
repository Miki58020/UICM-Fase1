<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $fillable = [
        'matricula', 'user_id', 'aspirante_id', 'programa_id', 'periodo_id', 'grupo_id',
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

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
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

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }

    /**
     * Recalcula creditos_acumulados a partir de las calificaciones cuya
     * carga académica ya fue aprobada por control escolar.
     */
    public function recalcularCreditosAcumulados(): void
    {
        $porCarga = $this->calificaciones()
            ->with('cargaAcademica.materia')
            ->get()
            ->groupBy('carga_academica_id');

        $creditos = 0;
        foreach ($porCarga as $cargaId => $registros) {
            $carga = $registros->first()?->cargaAcademica;
            $materia = $carga?->materia;

            if (!$materia || $carga->estado_revision !== 'aprobado') {
                continue;
            }

            $final = Calificacion::finalPara($this->id, $cargaId, $registros);
            if ($final && $final->calificacion >= 7.0) {
                $creditos += $materia->creditos ?? 0;
            }
        }

        $this->update(['creditos_acumulados' => $creditos]);
    }
}
