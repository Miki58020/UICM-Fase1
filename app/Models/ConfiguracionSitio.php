<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSitio extends Model
{
    protected $table = 'configuracion_sitio';

    protected $fillable = ['clave', 'valor'];

    public static function get(string $clave, string $default = ''): string
    {
        return static::where('clave', $clave)->value('valor') ?? $default;
    }

    public static function set(string $clave, string $valor): void
    {
        static::updateOrInsert(['clave' => $clave], ['valor' => $valor]);
    }
}
