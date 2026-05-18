<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracionMercadopago;

class ConfiguracionMercadopagoSeeder extends Seeder
{
    public function run(): void
    {
        if (ConfiguracionMercadopago::exists()) {
            return;
        }

        ConfiguracionMercadopago::create([
            'public_key'       => env('MERCADOPAGO_PUBLIC_KEY', ''),
            'access_token'     => env('MERCADOPAGO_ACCESS_TOKEN', ''),
            'back_url_success' => env('APP_URL', '') . '/aspirante/pago/retorno',
            'back_url_pending' => env('APP_URL', '') . '/aspirante/pago/retorno',
            'back_url_failure' => env('APP_URL', '') . '/aspirante/pago/retorno',
            'notification_url' => null,
            'activo'           => true,
        ]);
    }
}
