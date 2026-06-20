<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    protected $fillable = ['alumno_id', 'carga_academica_id', 'tipo', 'calificacion'];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function cargaAcademica()
    {
        return $this->belongsTo(CargaAcademica::class);
    }

    public function aprobado(): bool
    {
        return $this->calificacion >= 7.0;
    }

    /**
     * Calificación final de un alumno para una carga académica: usa el
     * extraordinario si existe, si no la calificación final capturada.
     */
    public static function finalPara($alumnoId, $cargaAcademicaId, $calificaciones = null): ?self
    {
        $calificaciones ??= self::where('alumno_id', $alumnoId)
            ->where('carga_academica_id', $cargaAcademicaId)
            ->get();

        return $calificaciones->firstWhere('tipo', 'extraordinario')
            ?? $calificaciones->firstWhere('tipo', 'final');
    }
}
