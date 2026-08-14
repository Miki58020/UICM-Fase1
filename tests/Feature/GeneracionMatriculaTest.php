<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Periodo;
use App\Models\PeriodoPrograma;
use App\Models\Programa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * CB-01 — Generacion de la matricula institucional.
 *
 * La matricula se compone leyendo Alumno::generarMatricula(): dos digitos de anio,
 * un digito de periodo derivado del mes de inicio de registro, el numero de
 * generacion, el numero de carrera y un consecutivo de cuatro digitos.
 *
 * Desde la interfaz solo se observa el numero final. Estas pruebas verifican cada
 * componente por separado y, sobre todo, la tabla de decision del digito de
 * periodo, cuyas tres ramas no son observables desde fuera.
 */
class GeneracionMatriculaTest extends TestCase
{
    use RefreshDatabase;

    private function programa(int $numeroCarrera = 3): Programa
    {
        return Programa::create([
            'clave'                  => 'PRG' . $numeroCarrera,
            'nombre'                 => 'Programa de prueba ' . $numeroCarrera,
            'nivel'                  => 'licenciatura',
            'duracion_cuatrimestres' => 9,
            'total_creditos'         => 300,
            'activo'                 => true,
            'numero_carrera'         => $numeroCarrera,
        ]);
    }

    private function periodo(string $inicioRegistro): Periodo
    {
        return Periodo::create([
            'nombre'                => 'PER-' . $inicioRegistro,
            'label'                 => 'Periodo de prueba',
            'estado'                => 'abierto',
            'fecha_inicio_registro' => $inicioRegistro,
        ]);
    }

    public function test_compone_la_matricula_con_anio_periodo_generacion_carrera_y_consecutivo(): void
    {
        $programa = $this->programa(3);
        $periodo  = $this->periodo('2026-09-01');   // septiembre -> periodo 1

        PeriodoPrograma::create([
            'periodo_id'        => $periodo->id,
            'programa_id'       => $programa->id,
            'numero_carrera'    => 3,
            'numero_generacion' => 2,
            'activo'            => true,
        ]);

        $matricula = Alumno::generarMatricula($periodo, $programa);

        // 26 (anio) + 1 (periodo) + 2 (generacion) + 3 (carrera) + 0001 (consecutivo)
        $this->assertSame('261230001', $matricula);
        $this->assertSame(9, strlen($matricula));
    }

    /**
     * El digito de periodo sale de una expresion match sobre el mes de inicio de
     * registro. Son tres ramas y ninguna se puede provocar desde la interfaz sin
     * crear periodos con fechas especificas.
     */
    #[DataProvider('mesesYPeriodos')]
    public function test_el_digito_de_periodo_corresponde_al_mes_de_inicio(string $fecha, string $digitoEsperado): void
    {
        $programa = $this->programa(1);
        $periodo  = $this->periodo($fecha);

        PeriodoPrograma::create([
            'periodo_id'        => $periodo->id,
            'programa_id'       => $programa->id,
            'numero_carrera'    => 1,
            'numero_generacion' => 1,
            'activo'            => true,
        ]);

        $matricula = Alumno::generarMatricula($periodo, $programa);

        // El tercer caracter es el digito de periodo.
        $this->assertSame($digitoEsperado, substr($matricula, 2, 1));
    }

    public static function mesesYPeriodos(): array
    {
        return [
            'septiembre cae en el periodo 1' => ['2026-09-15', '1'],
            'diciembre cae en el periodo 1'  => ['2026-12-01', '1'],
            'enero cae en el periodo 2'      => ['2026-01-10', '2'],
            'abril cae en el periodo 2'      => ['2026-04-30', '2'],
            'mayo cae en el periodo 3'       => ['2026-05-01', '3'],
            'agosto cae en el periodo 3'     => ['2026-08-31', '3'],
        ];
    }

    public function test_el_consecutivo_avanza_con_cada_alumno_del_mismo_programa_y_periodo(): void
    {
        $programa = $this->programa(4);
        $periodo  = $this->periodo('2026-09-01');

        PeriodoPrograma::create([
            'periodo_id'        => $periodo->id,
            'programa_id'       => $programa->id,
            'numero_carrera'    => 4,
            'numero_generacion' => 1,
            'activo'            => true,
        ]);

        $primera = Alumno::generarMatricula($periodo, $programa);

        Alumno::create([
            'matricula'        => $primera,
            'programa_id'      => $programa->id,
            'periodo_id'       => $periodo->id,
            'nombre'           => 'ALUMNO',
            'apellido_paterno' => 'DE PRUEBA',
            'email'            => 'consecutivo@example.com',
        ]);

        $segunda = Alumno::generarMatricula($periodo, $programa);

        $this->assertSame('0001', substr($primera, -4));
        $this->assertSame('0002', substr($segunda, -4));
    }

    /**
     * El consecutivo se calcula por programa y periodo, no de forma global: dos
     * programas distintos deben arrancar cada uno en 0001.
     */
    public function test_el_consecutivo_es_independiente_entre_programas(): void
    {
        $periodo   = $this->periodo('2026-09-01');
        $programaA = $this->programa(5);
        $programaB = $this->programa(6);

        foreach ([$programaA, $programaB] as $p) {
            PeriodoPrograma::create([
                'periodo_id'        => $periodo->id,
                'programa_id'       => $p->id,
                'numero_carrera'    => $p->numero_carrera,
                'numero_generacion' => 1,
                'activo'            => true,
            ]);
        }

        Alumno::create([
            'matricula'        => Alumno::generarMatricula($periodo, $programaA),
            'programa_id'      => $programaA->id,
            'periodo_id'       => $periodo->id,
            'nombre'           => 'ALUMNO',
            'apellido_paterno' => 'PROGRAMA A',
            'email'            => 'programa.a@example.com',
        ]);

        $this->assertSame('0001', substr(Alumno::generarMatricula($periodo, $programaB), -4));
    }
}
