<?php

namespace App\Console\Commands;

use App\Models\Periodo;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SincronizarPeriodos extends Command
{
    protected $signature   = 'periodos:sincronizar';
    protected $description = 'Abre y cierra automáticamente los periodos de registro según sus fechas (solo los que tienen auto=true).';

    public function handle(): void
    {
        $hoy = Carbon::today();

        // Abrir periodos que ya llegaron a su fecha de apertura
        $abiertos = Periodo::where('auto', true)
            ->where('estado', 'inactivo')
            ->where('fecha_inicio_registro', '<=', $hoy)
            ->where('fecha_fin_registro', '>=', $hoy)
            ->get();

        foreach ($abiertos as $periodo) {
            $periodo->update(['estado' => 'activo']);
            $this->info("Activado: {$periodo->label}");
        }

        // Cerrar periodos que ya pasaron su fecha de cierre
        $cerrados = Periodo::where('auto', true)
            ->where('estado', 'activo')
            ->where('fecha_fin_registro', '<', $hoy)
            ->get();

        foreach ($cerrados as $periodo) {
            $periodo->update(['estado' => 'cerrado']);
            $this->info("Cerrado: {$periodo->label}");
        }

        $this->info('Sincronización completada.');
    }
}
