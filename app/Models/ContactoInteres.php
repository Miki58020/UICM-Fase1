<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoInteres extends Model
{
    protected $table = 'contacto_intereses';

    protected $fillable = ['etiqueta', 'orden', 'activo'];
}
