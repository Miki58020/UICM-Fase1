<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * CB-02 — Control de acceso basado en roles (middleware CheckRol).
 *
 * El middleware concentra cuatro caminos distintos, y solo el primero es evidente
 * desde la interfaz:
 *
 *   1. Peticion sin sesion                      -> redirige al login
 *   2. Rol distinto al exigido                  -> 403, salvo que sea admin
 *   3. Alumno cuya cuenta dejo de estar activa  -> cierre de sesion y aviso
 *   4. Profesor cuya cuenta fue desactivada     -> cierre de sesion y aviso
 *
 * Los dos ultimos dependen del estado del alumno o del profesor en la base de
 * datos, no de su rol, y comprobarlos a mano exigiria dar de baja a una persona
 * mientras tiene la sesion abierta.
 *
 * Las aserciones se limitan a lo que decide el middleware —dejar pasar o no—, sin
 * depender de lo que cada controlador termine devolviendo.
 */
class ControlAccesoRolTest extends TestCase
{
    use RefreshDatabase;

    /** Ruta protegida representativa de cada rol administrativo. */
    private const RUTAS = [
        'control_escolar' => 'admin.alumnos.index',
        'finanzas'        => 'finanzas.pagos.index',
        'coordinacion'    => 'admin.programas.index',
    ];

    private function usuario(string $rol): User
    {
        return User::create([
            'name'     => 'Usuario ' . $rol,
            'email'    => $rol . '@uicm.test',
            'password' => bcrypt('secret-de-prueba'),
            'rol'      => $rol,
        ]);
    }

    private function programa(): Programa
    {
        return Programa::firstOrCreate(
            ['clave' => 'TEST'],
            [
                'nombre'                 => 'Programa de prueba',
                'nivel'                  => 'licenciatura',
                'duracion_cuatrimestres' => 9,
                'total_creditos'         => 300,
                'activo'                 => true,
                'numero_carrera'         => 1,
            ]
        );
    }

    public function test_una_peticion_sin_sesion_no_alcanza_una_ruta_protegida(): void
    {
        $this->get(route(self::RUTAS['control_escolar']))->assertRedirect('/login');
    }

    /**
     * Cada rol solo entra en lo suyo. Se recorren todas las combinaciones de rol
     * contra ruta ajena, que es justo lo que a mano resulta tedioso y poco fiable.
     */
    public function test_ningun_rol_alcanza_las_rutas_de_otro(): void
    {
        foreach (self::RUTAS as $rol => $_) {
            $usuario = $this->usuario($rol);

            foreach (self::RUTAS as $rolDeLaRuta => $nombreRuta) {
                $respuesta = $this->actingAs($usuario)->get(route($nombreRuta));

                if ($rol === $rolDeLaRuta) {
                    $this->assertNotSame(403, $respuesta->getStatusCode(),
                        "El rol {$rol} deberia entrar a su propia ruta");
                } else {
                    $respuesta->assertForbidden();
                }
            }
        }
    }

    public function test_el_rol_admin_atraviesa_cualquier_ruta(): void
    {
        $admin = $this->usuario('admin');

        foreach (self::RUTAS as $nombreRuta) {
            $this->assertNotSame(
                403,
                $this->actingAs($admin)->get(route($nombreRuta))->getStatusCode(),
                "El administrador deberia atravesar {$nombreRuta}"
            );
        }
    }

    /**
     * Rama 3: la cuenta del alumno se revisa en cada peticion, no solo al iniciar
     * sesion. Si deja de estar activa mientras navega, se le cierra la sesion.
     */
    #[DataProvider('estadosNoActivos')]
    public function test_al_alumno_que_deja_de_estar_activo_se_le_cierra_la_sesion(string $estado, string $fragmento): void
    {
        $usuario = $this->usuario('alumno');

        Alumno::create([
            'matricula'        => '260000001',
            'user_id'          => $usuario->id,
            'programa_id'      => $this->programa()->id,
            'nombre'           => 'ALUMNO',
            'apellido_paterno' => 'DE PRUEBA',
            'email'            => 'alumno.estado@example.com',
            'estado'           => $estado,
        ]);

        $respuesta = $this->actingAs($usuario)->get(route('alumno.dashboard'));

        $respuesta->assertRedirect(route('login'));
        $respuesta->assertSessionHasErrors('email');
        $this->assertStringContainsString($fragmento, session('errors')->first('email'));
        $this->assertGuest();
    }

    public static function estadosNoActivos(): array
    {
        return [
            'dado de baja'      => ['baja', 'dada de baja'],
            'inactivo temporal' => ['inactivo', 'inactiva temporalmente'],
        ];
    }

    /** Rama 4: el mismo control, pero sobre la cuenta del profesor. */
    public function test_al_profesor_desactivado_se_le_cierra_la_sesion(): void
    {
        $usuario = $this->usuario('profesor');

        Profesor::create([
            'user_id' => $usuario->id,
            'nombre'  => 'PROFESOR DE PRUEBA',
            'correo'  => 'profesor.estado@example.com',
            'activo'  => false,
        ]);

        $respuesta = $this->actingAs($usuario)->get(route('profesor.calificaciones.index'));

        $respuesta->assertRedirect(route('login'));
        $respuesta->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** El alumno activo si atraviesa el middleware. */
    public function test_el_alumno_activo_conserva_el_acceso(): void
    {
        $usuario = $this->usuario('alumno');

        Alumno::create([
            'matricula'        => '260000002',
            'user_id'          => $usuario->id,
            'programa_id'      => $this->programa()->id,
            'nombre'           => 'ALUMNO',
            'apellido_paterno' => 'ACTIVO',
            'email'            => 'alumno.activo@example.com',
            'estado'           => 'activo',
        ]);

        $respuesta = $this->actingAs($usuario)->get(route('alumno.dashboard'));

        $this->assertNotSame(403, $respuesta->getStatusCode());
        $this->assertAuthenticated();
    }
}
