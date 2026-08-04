<?php

namespace Database\Seeders;

use App\Models\TarifaInscripcion;
use Illuminate\Database\Seeder;

class TarifaInscripcionSeeder extends Seeder
{
    public function run(): void
    {
        $tarifas = [
            ['nivel' => 'licenciatura', 'monto' => 3000.00],
            ['nivel' => 'maestria',     'monto' => 4000.00],
            ['nivel' => 'doctorado',    'monto' => 5000.00],
        ];

        foreach ($tarifas as $tarifa) {
            TarifaInscripcion::updateOrCreate(
                ['nivel' => $tarifa['nivel'], 'tipo' => 'inscripcion'],
                ['monto' => $tarifa['monto']]
            );
        }
    }
}
