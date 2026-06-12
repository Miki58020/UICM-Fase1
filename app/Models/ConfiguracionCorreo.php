<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionCorreo extends Model
{
    protected $table = 'configuracion_correo';

    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'from_address',
        'from_name',
        'activo',
    ];

    protected $casts = [
        'port'     => 'integer',
        'activo'   => 'boolean',
        'password' => 'encrypted',
    ];

    public static function activa(): ?self
    {
        return self::where('activo', true)->first();
    }

    public function passwordSeguro(): ?string
    {
        try {
            return $this->password;
        } catch (\Illuminate\Contracts\Encryption\DecryptException) {
            return null;
        }
    }
}
