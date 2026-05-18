<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionMercadopago extends Model
{
    protected $table = 'configuracion_mercadopago';

    protected $fillable = [
        'public_key',
        'access_token',
        'back_url_success',
        'back_url_pending',
        'back_url_failure',
        'notification_url',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public static function activa(): ?self
    {
        return self::where('activo', true)->first();
    }
}
