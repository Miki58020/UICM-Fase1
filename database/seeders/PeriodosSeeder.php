<?php

namespace Database\Seeders;

use App\Models\Periodo;
use Illuminate\Database\Seeder;

class PeriodosSeeder extends Seeder
{
    public function run(): void
    {
        $periodos = [
            [
                'nombre'                => '2026-1',
                'label'                 => 'Primer Cuatrimestre 2026',
                'fecha_inicio_registro' => '2025-12-01',
                'fecha_fin_registro'    => '2026-01-11',
                'fecha_inicio_clases'   => '2026-01-12',
                'fecha_fin_clases'      => '2026-04-25',
                'estado'                => 'activo',
            ],
            [
                'nombre'                => '2026-2',
                'label'                 => 'Segundo Cuatrimestre 2026',
                'fecha_inicio_registro' => '2026-04-01',
                'fecha_fin_registro'    => '2026-05-03',
                'fecha_inicio_clases'   => '2026-05-04',
                'fecha_fin_clases'      => '2026-08-14',
                'estado'                => 'inactivo',
            ],
            [
                'nombre'                => '2026-3',
                'label'                 => 'Tercer Cuatrimestre 2026',
                'fecha_inicio_registro' => '2026-07-15',
                'fecha_fin_registro'    => '2026-08-23',
                'fecha_inicio_clases'   => '2026-08-24',
                'fecha_fin_clases'      => '2026-12-04',
                'estado'                => 'inactivo',
            ],
        ];

        foreach ($periodos as $data) {
            Periodo::updateOrCreate(['nombre' => $data['nombre']], $data);
        }
    }
}
