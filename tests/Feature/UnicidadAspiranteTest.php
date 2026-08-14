<?php

namespace Tests\Feature;

use App\Models\Aspirante;
use App\Models\Periodo;
use App\Models\PeriodoPrograma;
use App\Models\Programa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * CB-04 — Unicidad compuesta de correo y CURP por programa.
 *
 * La regla no impide que un correo o una CURP se repitan en el sistema: impide que
 * se repitan *dentro del mismo programa*. Es lo que permite que una misma persona
 * curse dos programas a la vez, y a la vez evita que duplique su solicitud.
 *
 * La regla se aplica sobre dos columnas, de modo que su comportamiento solo queda
 * demostrado recorriendo las cuatro combinaciones posibles. A mano exigiria cuatro
 * registros completos, con sus seis documentos adjuntos cada uno.
 */
class UnicidadAspiranteTest extends TestCase
{
    use RefreshDatabase;

    private const PERIODO = 'PRUEBA-2026';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Mail::fake();

        $periodo = Periodo::create([
            'nombre'                => self::PERIODO,
            'label'                 => 'Periodo de prueba',
            'estado'                => 'activo',
            'fecha_inicio_registro' => now()->subDay()->toDateString(),
            'fecha_fin_registro'    => now()->addMonth()->toDateString(),
        ]);

        foreach ([['PA', 1], ['PB', 2]] as [$clave, $numero]) {
            $programa = Programa::create([
                'clave'                  => $clave,
                'nombre'                 => 'Programa ' . $clave,
                'nivel'                  => 'licenciatura',
                'duracion_cuatrimestres' => 9,
                'total_creditos'         => 300,
                'activo'                 => true,
                'numero_carrera'         => $numero,
            ]);

            PeriodoPrograma::create([
                'periodo_id'        => $periodo->id,
                'programa_id'       => $programa->id,
                'numero_carrera'    => $numero,
                'numero_generacion' => 1,
                'activo'            => true,
            ]);
        }
    }

    /**
     * @param string $clavePrograma clave del programa al que se aplica
     */
    private function registrar(string $clavePrograma, string $email, string $curp): \Illuminate\Testing\TestResponse
    {
        return $this->post('/registro', [
            'nombre'             => 'PERSONA',
            'apellido_paterno'   => 'DE PRUEBA',
            'apellido_materno'   => 'CASO',
            'curp'               => $curp,
            'fecha_nacimiento'   => '2000-03-15',
            'telefono'           => '5512345678',
            'email'              => $email,
            'programa_academico' => $clavePrograma,
            'generacion'         => self::PERIODO,

            // create() en lugar de image(): el entorno local no tiene la extension GD,
            // y para esta prueba basta con que el archivo declare un tipo admitido.
            'acta_nacimiento'       => UploadedFile::fake()->create('acta.jpg', 120, 'image/jpeg'),
            'curp_doc'              => UploadedFile::fake()->create('curp.jpg', 120, 'image/jpeg'),
            'identificacion'        => UploadedFile::fake()->create('ine.jpg', 120, 'image/jpeg'),
            'comprobante_domicilio' => UploadedFile::fake()->create('domicilio.jpg', 120, 'image/jpeg'),
            'foto'                  => UploadedFile::fake()->create('foto.jpg', 120, 'image/jpeg'),
            'certificado'           => UploadedFile::fake()->create('certificado.jpg', 120, 'image/jpeg'),
        ]);
    }

    public function test_el_mismo_correo_y_curp_se_admiten_en_programas_distintos(): void
    {
        $email = 'doble.carrera@example.com';
        $curp  = 'MAGL950301HDFRTS03';

        $this->registrar('PA', $email, $curp)->assertSessionHasNoErrors();
        $this->registrar('PB', $email, $curp)->assertSessionHasNoErrors();

        $this->assertSame(2, Aspirante::where('email', $email)->count());

        // Cada solicitud queda en su propio programa y con folio propio.
        $folios = Aspirante::where('email', $email)->pluck('folio');
        $this->assertCount(2, $folios->unique());
    }

    public function test_el_mismo_correo_se_rechaza_dentro_del_mismo_programa(): void
    {
        $email = 'repetido@example.com';

        $this->registrar('PA', $email, 'MAGL950301HDFRTS03')->assertSessionHasNoErrors();

        // Segunda solicitud al mismo programa, cambiando la CURP para aislar el correo.
        $this->registrar('PA', $email, 'LOPZ950215MDFPRZ05')->assertSessionHasErrors('email');

        $this->assertSame(1, Aspirante::where('email', $email)->count());
    }

    public function test_la_misma_curp_se_rechaza_dentro_del_mismo_programa(): void
    {
        $curp = 'SAHR980712HJCLMN08';

        $this->registrar('PA', 'uno@example.com', $curp)->assertSessionHasNoErrors();

        // Segunda solicitud al mismo programa, cambiando el correo para aislar la CURP.
        $this->registrar('PA', 'dos@example.com', $curp)->assertSessionHasErrors('curp');

        $this->assertSame(1, Aspirante::where('curp', $curp)->count());
    }

    /** Sin periodo activo el formulario no se procesa, con independencia de los datos. */
    public function test_sin_periodo_activo_no_se_admite_ninguna_solicitud(): void
    {
        Periodo::query()->update(['estado' => 'cerrado']);

        $this->registrar('PA', 'fuera.de.plazo@example.com', 'MAGL950301HDFRTS03')
            ->assertSessionHasErrors('generacion');

        $this->assertSame(0, Aspirante::count());
    }
}
