<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracionCorreo;

class ConfiguracionCorreoSeeder extends Seeder
{
    public function run(): void
    {
        if (ConfiguracionCorreo::exists()) {
            return;
        }

        ConfiguracionCorreo::create([
            'mailer'       => env('MAIL_MAILER', 'smtp'),
            'host'         => env('MAIL_HOST', ''),
            'port'         => env('MAIL_PORT', 2525),
            'username'     => env('MAIL_USERNAME', ''),
            'password'     => env('MAIL_PASSWORD', ''),
            'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@uicm.edu.mx'),
            'from_name'    => env('MAIL_FROM_NAME', 'UICM'),
            'activo'       => true,
        ]);
    }
}
