<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * CB-03 — Bloqueo progresivo del inicio de sesion (LoginRequest).
 *
 * Tras cinco intentos fallidos la cuenta queda bloqueada, y la duracion del bloqueo
 * escala en cada reincidencia siguiendo la escala 1, 5, 15, 30 y 60 minutos.
 *
 * Comprobar esto a mano exige encadenar cinco intentos sin equivocarse y despues
 * esperar a que expire la ventana para provocar el segundo bloqueo: una espera de
 * un minuto en el mejor de los casos. Automatizado, la ventana se puede vaciar sin
 * esperar, conservando el contador de reincidencias, que es justamente la variable
 * cuyo efecto interesa observar.
 */
class RateLimitingLoginTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'password-correcto';
    private const EMAIL    = 'usuario@uicm.test';

    private function usuario(): User
    {
        return User::create([
            'name'     => 'Usuario de prueba',
            'email'    => self::EMAIL,
            'password' => bcrypt(self::PASSWORD),
            'rol'      => 'admin',
        ]);
    }

    /** Misma clave que arma LoginRequest::throttleKey(). */
    private function clave(): string
    {
        return Str::transliterate(Str::lower(self::EMAIL) . '|127.0.0.1');
    }

    private function intentarConPasswordIncorrecto(): \Illuminate\Testing\TestResponse
    {
        return $this->post('/login', [
            'email'    => self::EMAIL,
            'password' => 'password-equivocado',
        ]);
    }

    public function test_el_sexto_intento_queda_bloqueado(): void
    {
        $this->usuario();

        for ($i = 1; $i <= 5; $i++) {
            $this->intentarConPasswordIncorrecto()->assertSessionHasErrors('email');
            $this->assertSame($i, RateLimiter::attempts($this->clave()));
        }

        $this->assertTrue(RateLimiter::tooManyAttempts($this->clave(), 5));

        // El sexto ya no llega a comprobar la contrasena: lo detiene el limitador.
        $this->intentarConPasswordIncorrecto()->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_la_contrasena_correcta_no_sirve_mientras_dura_el_bloqueo(): void
    {
        $this->usuario();

        for ($i = 1; $i <= 5; $i++) {
            $this->intentarConPasswordIncorrecto();
        }

        // Aun con las credenciales validas, el bloqueo se antepone.
        $this->post('/login', [
            'email'    => self::EMAIL,
            'password' => self::PASSWORD,
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_la_duracion_del_bloqueo_escala_en_cada_reincidencia(): void
    {
        $this->usuario();

        // Primer bloqueo: la escala arranca en 60 segundos.
        for ($i = 1; $i <= 5; $i++) {
            $this->intentarConPasswordIncorrecto();
        }

        $primerBloqueo = RateLimiter::availableIn($this->clave());
        $this->assertGreaterThan(0, $primerBloqueo);
        $this->assertLessThanOrEqual(60, $primerBloqueo);

        // Se vacia la ventana como si hubiera expirado, sin tocar el contador de
        // reincidencias: es el escenario del usuario que vuelve a intentarlo mas tarde.
        RateLimiter::clear($this->clave());
        $this->assertSame(1, Cache::get('login_nivel_bloqueo:' . $this->clave()));

        // El primer fallo de la ventana nueva ya arrastra el nivel acumulado.
        $this->intentarConPasswordIncorrecto();

        $segundoBloqueo = RateLimiter::availableIn($this->clave());
        $this->assertGreaterThan(60, $segundoBloqueo,
            'El segundo bloqueo deberia durar mas que el primero');
        $this->assertLessThanOrEqual(300, $segundoBloqueo);
    }

    public function test_un_ingreso_correcto_limpia_el_contador_y_el_nivel(): void
    {
        $this->usuario();

        $this->intentarConPasswordIncorrecto();
        $this->intentarConPasswordIncorrecto();
        $this->assertSame(2, RateLimiter::attempts($this->clave()));

        $this->post('/login', [
            'email'    => self::EMAIL,
            'password' => self::PASSWORD,
        ]);

        $this->assertAuthenticated();
        $this->assertSame(0, RateLimiter::attempts($this->clave()));
        $this->assertNull(Cache::get('login_nivel_bloqueo:' . $this->clave()));
    }

    /** El limitador distingue por correo: bloquear a uno no afecta a los demas. */
    public function test_el_bloqueo_no_alcanza_a_otras_cuentas(): void
    {
        $this->usuario();

        User::create([
            'name'     => 'Otro usuario',
            'email'    => 'otro@uicm.test',
            'password' => bcrypt(self::PASSWORD),
            'rol'      => 'admin',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $this->intentarConPasswordIncorrecto();
        }

        $this->post('/login', [
            'email'    => 'otro@uicm.test',
            'password' => self::PASSWORD,
        ]);

        $this->assertAuthenticated();
    }
}
