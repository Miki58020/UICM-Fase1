<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('periodos:sincronizar')->dailyAt('00:05');
Schedule::command('uicm:resumen-diario')->dailyAt('09:00');
Schedule::command('pagos:generar-colegiatura')->monthlyOn(1, '01:00');
