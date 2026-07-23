@extends('layouts.app')

@section('title', 'Horario | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Profesor</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Horario</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5
                             a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700">Detalle del horario</h2>
                <span class="ml-auto text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green">
                    {{ $cargas->count() }} {{ $cargas->count() === 1 ? 'grupo' : 'grupos' }}
                </span>
            </div>

            <div class="overflow-x-auto">
                @if ($cargas->isEmpty())
                    <div class="px-6 py-10">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin horario asignado</h3>
                            <p class="text-sm text-gray-500 max-w-xs mx-auto">Aún no tienes grupos ni materias asignadas.</p>
                        </div>
                    </div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Período</th>
                            <th class="px-6 py-3">Grupo</th>
                            <th class="px-6 py-3">Materia</th>
                            <th class="px-6 py-3">Horario</th>
                            <th class="px-6 py-3">Aula virtual</th>
                            <th class="px-6 py-3">Fechas de captura</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($cargas as $carga)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-600 text-xs">
                                {{ $carga->periodo->nombre ?? '—' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-700">
                                {{ $carga->grupo->clave ?? '—' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $carga->materia->nombre ?? '—' }}
                                <span class="block text-xs text-gray-400">{{ $carga->materia->clave ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs">
                                {{ $carga->horario_formateado ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs">
                                {{ $carga->aula ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                                @if ($carga->fecha_inicio && $carga->fecha_fin)
                                    {{ $carga->fecha_inicio->format('d/m/Y') }} - {{ $carga->fecha_fin->format('d/m/Y') }}
                                @else
                                    <span class="text-orange-500 font-medium">Sin definir</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        {{-- Vista semanal (solo el período más reciente, para no mezclar cuatrimestres distintos) --}}
        @php
            $diasLabel   = ['lunes'=>'Lun','martes'=>'Mar','miercoles'=>'Mié','jueves'=>'Jue','viernes'=>'Vie','sabado'=>'Sáb','domingo'=>'Dom'];
            $periodoTop  = $cargas->max('periodo_id');
            $cargasTop   = $cargas->where('periodo_id', $periodoTop);
            $conHorario  = $cargasTop->filter(fn($c) => $c->dia_semana && $c->hora_inicio && $c->hora_fin);
            $toMin       = fn($t) => ((int) explode(':', $t)[0]) * 60 + (int) explode(':', $t)[1];

            $diasMostrar = collect(['lunes','martes','miercoles','jueves','viernes','sabado']);
            if ($conHorario->contains('dia_semana', 'domingo')) {
                $diasMostrar->push('domingo');
            }

            $tint = function (string $hex, float $pct) {
                [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
                return sprintf('#%02X%02X%02X', $r + (255 - $r) * $pct, $g + (255 - $g) * $pct, $b + (255 - $b) * $pct);
            };
            $shade = function (string $hex, float $pct) {
                [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
                return sprintf('#%02X%02X%02X', $r * (1 - $pct), $g * (1 - $pct), $b * (1 - $pct));
            };

            $baseColores = ['#0F4229', '#D4AF37', '#EFAD5A', '#1F5FBF'];
            $tonalidades = [0.82, 0.60]; // clara, intensa

            $paleta = [];
            foreach ($baseColores as $base) {
                foreach ($tonalidades as $t) {
                    $paleta[] = [
                        'bg'    => $tint($base, $t),
                        'text'  => $shade($base, 0.25),
                        'borde' => $base,
                    ];
                }
            }

            // Color por grupo (no por materia): un profesor puede repetir la misma
            // materia en secciones distintas y necesita distinguir a simple vista
            // a qué grupo pertenece cada bloque.
            $colorPorGrupo = [];
            foreach ($conHorario as $c) {
                if (!isset($colorPorGrupo[$c->grupo_id])) {
                    $colorPorGrupo[$c->grupo_id] = $paleta[count($colorPorGrupo) % count($paleta)];
                }
            }
        @endphp

        @if($conHorario->isNotEmpty())
            @php
                $minIni    = $conHorario->map(fn($c) => $toMin($c->hora_inicio))->min();
                $maxFin    = $conHorario->map(fn($c) => $toMin($c->hora_fin))->max();
                $rangoIni  = intdiv($minIni, 60) * 60;
                $rangoFin  = (intdiv($maxFin - 1, 60) + 1) * 60;
                $totalHrs  = max(1, intdiv($rangoFin - $rangoIni, 60));
                $pxHora    = 64;
                $altoTotal = $totalHrs * $pxHora;
            @endphp
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Vista semanal</h2>
                    <span class="ml-auto text-xs text-gray-400">{{ $cargasTop->first()->periodo->nombre ?? '' }}</span>
                </div>
                <div class="p-4 sm:p-6 overflow-x-auto">
                    <div class="flex" style="min-width: 640px;">
                        {{-- Columna de horas --}}
                        <div class="flex-shrink-0" style="width: 52px;">
                            <div style="height: 26px;"></div>
                            @for ($h = 0; $h < $totalHrs; $h++)
                                <div class="text-[11px] text-gray-400 text-right pr-2" style="height: {{ $pxHora }}px; margin-top: -6px;">
                                    {{ str_pad((int) (($rangoIni / 60) + $h), 2, '0', STR_PAD_LEFT) }}:00
                                </div>
                            @endfor
                        </div>

                        {{-- Columnas de días --}}
                        @foreach ($diasMostrar as $dia)
                        <div class="flex-1 min-w-[92px]">
                            <div class="text-center text-xs font-bold text-gray-500 uppercase tracking-wide" style="height: 26px;">
                                {{ $diasLabel[$dia] }}
                            </div>
                            <div class="relative border-l border-gray-100" style="height: {{ $altoTotal }}px;">
                                @for ($h = 0; $h < $totalHrs; $h++)
                                    <div class="absolute left-0 right-0 border-t border-gray-100" style="top: {{ $h * $pxHora }}px;"></div>
                                @endfor

                                @foreach ($conHorario->where('dia_semana', $dia) as $c)
                                    @php
                                        $ini   = $toMin($c->hora_inicio);
                                        $fin   = $toMin($c->hora_fin);
                                        $top   = ($ini - $rangoIni) / 60 * $pxHora;
                                        $alto  = max(30, ($fin - $ini) / 60 * $pxHora - 2);
                                        $color = $colorPorGrupo[$c->grupo_id];
                                    @endphp
                                    <div class="absolute left-1 right-1 rounded-lg px-2 py-1 overflow-hidden"
                                         style="top: {{ $top }}px; height: {{ $alto }}px; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-left: 3px solid {{ $color['borde'] }};"
                                         title="{{ $c->materia->nombre }} · {{ $c->grupo->clave ?? '' }} · {{ $c->hora_inicio }} - {{ $c->hora_fin }}">
                                        <p class="text-[11px] font-bold leading-tight truncate">{{ $c->grupo->clave ?? $c->materia->nombre }}</p>
                                        <p class="text-[10px] leading-tight truncate opacity-80">{{ $c->materia->nombre }}</p>
                                        <p class="text-[10px] leading-tight truncate opacity-80">{{ $c->hora_inicio }}-{{ $c->hora_fin }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>
</section>
@endsection
