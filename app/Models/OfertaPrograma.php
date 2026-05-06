<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfertaPrograma extends Model
{
    protected $table = 'oferta_programas';

    protected $fillable = ['nombre', 'nivel', 'descripcion', 'puntos_clave', 'orden', 'activo'];

    protected $casts = [
        'puntos_clave' => 'array',
        'activo'       => 'boolean',
    ];
}
