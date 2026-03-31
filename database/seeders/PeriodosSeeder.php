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
                'nombre'       => '2026-1',
                'label'        => 'Primer Cuatrimestre 2026',
                'fecha_inicio' => '2026-01-12',
                'fecha_fin'    => '2026-04-25',
                'estado'       => 'activo',
            ],
            [
                'nombre'       => '2026-2',
                'label'        => 'Segundo Cuatrimestre 2026',
                'fecha_inicio' => '2026-05-04',
                'fecha_fin'    => '2026-08-14',
                'estado'       => 'proximo',
            ],
            [
                'nombre'       => '2026-3',
                'label'        => 'Tercer Cuatrimestre 2026',
                'fecha_inicio' => '2026-08-24',
                'fecha_fin'    => '2026-12-04',
                'estado'       => 'proximo',
            ],
        ];

        foreach ($periodos as $data) {
            Periodo::updateOrCreate(['nombre' => $data['nombre']], $data);
        }
    }
}
