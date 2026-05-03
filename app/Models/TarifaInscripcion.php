<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifaInscripcion extends Model
{
    protected $table    = 'tarifas_inscripcion';
    protected $fillable = ['nivel', 'monto'];
}
