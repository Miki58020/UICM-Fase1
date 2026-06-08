<?php

namespace App\Providers;

use App\Models\ConfiguracionCorreo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->aplicarConfiguracionCorreo();
    }

    /**
     * Sobreescribe la configuración de correo de .env con la guardada en la BD,
     * igual que MercadoPago: se administra desde el panel sin tocar código ni .env.
     */
    private function aplicarConfiguracionCorreo(): void
    {
        try {
            if (! Schema::hasTable('configuracion_correo')) {
                return;
            }

            $config = ConfiguracionCorreo::activa();

            if (! $config) {
                return;
            }

            config(['mail.default' => $config->mailer]);
            config(['mail.from' => [
                'address' => $config->from_address,
                'name'    => $config->from_name,
            ]]);

            if ($config->mailer === 'smtp') {
                config(['mail.mailers.smtp' => [
                    'transport'    => 'smtp',
                    'scheme'       => null,
                    'url'          => null,
                    'host'         => $config->host,
                    'port'         => $config->port,
                    'username'     => $config->username,
                    'password'     => $config->password,
                    'timeout'      => null,
                    'local_domain' => parse_url((string) config('app.url', 'http://localhost'), PHP_URL_HOST),
                ]]);
            }
        } catch (\Throwable $e) {
            // BD no disponible (p. ej. durante "migrate" antes de crear la tabla) — se usa la config de .env
        }
    }
}
