<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoPrograma extends Model
{
    protected $table = 'periodo_programa';

    protected $fillable = [
        'periodo_id', 'programa_id', 'numero_carrera', 'numero_generacion', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }
}
