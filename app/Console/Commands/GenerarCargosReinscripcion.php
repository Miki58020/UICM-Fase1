<?php

namespace App\Console\Commands;

use App\Models\Alumno;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\TarifaInscripcion;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerarCargosReinscripcion extends Command
{
    protected $signature = 'pagos:generar-reinscripcion';
    protected $description = 'Genera el cobro de reinscripción de cada alumno activo cuando se abre la ventana de pago del cuatrimestre (idempotente, no duplica si ya existe).';

    public function handle(): void
    {
        $periodo = Periodo::where('estado', 'activo')->first();

        if (!$periodo || !$periodo->fecha_fin_clases) {
            $this->info('No hay periodo activo con fecha_fin_clases configurada.');
            return;
        }

        $alumnos = Alumno::where('estado', 'activo')
            ->whereHas('grupo', fn($q) => $q->where('periodo_id', $periodo->id))
            ->with('programa', 'grupo')
            ->get();

        $generados = 0;
        $omitidos = 0;

        foreach ($alumnos as $alumno) {
            $tarifa = TarifaInscripcion::where('nivel', $alumno->programa?->nivel)
                ->where('tipo', 'cuatrimestre')
                ->first();

            $ventana = $tarifa?->ventanaPagoCuatrimestre($periodo);

            if (!$ventana || Carbon::today()->lt($ventana[0])) {
                $omitidos++;
                continue;
            }

            $pago = Pago::generarReinscripcion($alumno, $periodo);
            $pago ? $generados++ : $omitidos++;
        }

        $this->info("Cargos de reinscripción generados: {$generados}. Omitidos (ya existían, ventana cerrada o sin tarifa): {$omitidos}.");
    }
}
