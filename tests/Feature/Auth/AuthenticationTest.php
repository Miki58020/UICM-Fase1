<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Autenticacion y destino posterior al ingreso.
 *
 * Las expectativas originales de esta clase venian del paquete de autenticacion
 * con el que se inicio el proyecto y daban por supuesto un unico destino comun
 * (/dashboard) y una salida hacia la pagina publica. El sistema encamina a cada
 * usuario segun su rol y devuelve la sesion cerrada al formulario de acceso, de
 * modo que las aserciones se ajustaron al comportamiento real.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(string $rol): User
    {
        return User::factory()->create(['rol' => $rol]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    /**
     * El alumno tiene portal propio; el resto de los roles comparten el tablero
     * administrativo, que despues adapta su contenido.
     */
    #[DataProvider('rolesYDestinos')]
    public function test_el_ingreso_encamina_a_cada_rol_a_su_destino(string $rol, string $rutaEsperada): void
    {
        $usuario = $this->usuario($rol);

        $respuesta = $this->post('/login', [
            'email'    => $usuario->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $respuesta->assertRedirect(route($rutaEsperada, absolute: false));
    }

    public static function rolesYDestinos(): array
    {
        return [
            'alumno tiene portal propio'      => ['alumno', 'alumno.dashboard'],
            'profesor va al tablero'          => ['profesor', 'dashboard'],
            'control escolar va al tablero'   => ['control_escolar', 'dashboard'],
            'finanzas va al tablero'          => ['finanzas', 'dashboard'],
            'coordinacion va al tablero'      => ['coordinacion', 'dashboard'],
            'administracion va al tablero'    => ['admin', 'dashboard'],
        ];
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $usuario = $this->usuario('admin');

        $this->post('/login', [
            'email'    => $usuario->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /** Al cerrar sesion se vuelve al formulario de acceso, no a la pagina publica. */
    public function test_users_can_logout(): void
    {
        $usuario = $this->usuario('admin');

        $respuesta = $this->actingAs($usuario)->post('/logout');

        $this->assertGuest();
        $respuesta->assertRedirect('/login');
    }
}
