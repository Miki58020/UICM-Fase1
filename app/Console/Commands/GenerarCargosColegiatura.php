<?php

namespace App\Console\Commands;

use App\Models\Alumno;
use App\Models\Pago;
use App\Models\TarifaInscripcion;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerarCargosColegiatura extends Command
{
    protected $signature = 'pagos:generar-colegiatura';
    protected $description = 'Genera el cargo de colegiatura del mes actual para cada alumno activo (idempotente, no duplica si ya existe).';

    public function handle(): void
    {
        $hoy = Carbon::today();
        $mes = $hoy->format('Y-m');

        $alumnos = Alumno::where('estado', 'activo')
            ->whereNotNull('grupo_id')
            ->with('programa', 'grupo.periodo')
            ->get();

        $generados = 0;
        $omitidos = 0;

        foreach ($alumnos as $alumno) {
            if (!$alumno->programa) {
                $omitidos++;
                continue;
            }

            $yaExiste = Pago::where('alumno_id', $alumno->id)
                ->where('concepto', 'colegiatura')
                ->where('mes', $mes)
                ->exists();

            if ($yaExiste) {
                $omitidos++;
                continue;
            }

            $tarifa = TarifaInscripcion::where('nivel', $alumno->programa->nivel)
                ->where('tipo', 'colegiatura')
                ->first();

            if (!$tarifa) {
                $omitidos++;
                continue;
            }

            $inicioMes = $hoy->copy()->startOfMonth();
            $diaLimite = min($tarifa->dia_limite_pago ?? 10, $inicioMes->daysInMonth);

            Pago::create([
                'alumno_id'         => $alumno->id,
                'concepto'          => 'colegiatura',
                'periodo'           => $alumno->grupo->periodo->nombre ?? '',
                'mes'               => $mes,
                'monto'             => $tarifa->monto,
                'descuento'         => $tarifa->descuento,
                'monto_original'    => $tarifa->monto,
                'fecha_vencimiento' => $inicioMes->copy()->day($diaLimite),
                'estado'            => 'pendiente',
            ]);

            $generados++;
        }

        $this->info("Cargos de colegiatura generados: {$generados}. Omitidos (ya existían o sin tarifa): {$omitidos}.");
    }
}
