<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CarruselImagen extends Model
{
    protected $table = 'carrusel_imagenes';

    protected $fillable = ['imagen_path', 'orden', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->imagen_path);
    }
}
