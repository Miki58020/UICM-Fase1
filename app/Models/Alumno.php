<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $fillable = [
        'matricula', 'user_id', 'aspirante_id', 'programa_id', 'periodo_id', 'grupo_id',
        'nombre', 'apellido_paterno', 'apellido_materno', 'email',
        'cuatrimestre_actual', 'creditos_acumulados', 'estado', 'migrado',
        'curp', 'telefono', 'fecha_nacimiento',
    ];

    protected $casts = [
        'migrado' => 'boolean',
        'fecha_nacimiento' => 'date:Y-m-d',
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
     * Genera la siguiente matrícula disponible para un programa dentro de un periodo,
     * tomando en cuenta las matrículas ya existentes para ese periodo/programa.
     */
    public static function generarMatricula(Periodo $periodo, Programa $programa): string
    {
        $yy = $periodo->fecha_inicio_registro?->format('y') ?? now()->format('y');

        $mes = $periodo->fecha_inicio_registro
            ? (int) $periodo->fecha_inicio_registro->format('n')
            : (int) now()->format('n');

        $p = match (true) {
            $mes >= 9              => 1,
            $mes >= 1 && $mes <= 4 => 2,
            default                => 3,
        };

        $pp = PeriodoPrograma::where('periodo_id', $periodo->id)
            ->where('programa_id', $programa->id)
            ->first();

        $g = $pp?->numero_generacion ?? 1;
        $c = $pp?->numero_carrera ?? ($programa->numero_carrera ?? 0);

        $prefix = $yy . $p . $g . $c;

        $count = self::where('programa_id', $programa->id)
            ->where('periodo_id', $periodo->id)
            ->count() + 1;

        return $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
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
