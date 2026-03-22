<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudContrasena extends Model
{
    protected $table = 'solicitudes_contrasena';

    protected $fillable = ['user_id', 'estado'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
