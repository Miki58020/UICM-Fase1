<?php

namespace Database\Seeders;

use App\Models\Programa;
use Illuminate\Database\Seeder;

class ProgramasSeeder extends Seeder
{
    public function run(): void
    {
        $programas = [
            ['clave' => 'psicopedagogia',     'nombre' => 'Psicopedagogía',                      'nivel' => 'licenciatura', 'duracion_cuatrimestres' => 12],
            ['clave' => 'lengua_inglesa',      'nombre' => 'Lengua Inglesa',                       'nivel' => 'licenciatura', 'duracion_cuatrimestres' => 12],
            ['clave' => 'administracion',      'nombre' => 'Administración',                       'nivel' => 'licenciatura', 'duracion_cuatrimestres' => 12],
            ['clave' => 'lengua_literatura',   'nombre' => 'Lengua y Literatura',                  'nivel' => 'licenciatura', 'duracion_cuatrimestres' => 12],
            ['clave' => 'mba',                 'nombre' => 'Administración y Negocios',            'nivel' => 'maestria',     'duracion_cuatrimestres' => 6],
            ['clave' => 'maestria_educacion',  'nombre' => 'Educación',                            'nivel' => 'maestria',     'duracion_cuatrimestres' => 6],
            ['clave' => 'negocios_logistica',  'nombre' => 'Negocios y Logística Internacional',   'nivel' => 'maestria',     'duracion_cuatrimestres' => 6],
            ['clave' => 'doctorado_educacion', 'nombre' => 'Doctorado en Educación',               'nivel' => 'doctorado',    'duracion_cuatrimestres' => 9],
        ];

        foreach ($programas as $programa) {
            Programa::updateOrCreate(
                ['clave' => $programa['clave']],
                array_merge($programa, ['activo' => true])
            );
        }
    }
}
