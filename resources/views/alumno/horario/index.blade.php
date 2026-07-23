@extends('layouts.app')

@section('title', 'Horario | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Alumno</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Horario</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Contexto del grupo --}}
        <div class="mb-6 rounded-xl px-5 py-3 bg-white border border-gray-200 flex items-center gap-3 shadow-sm flex-wrap">
            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: #0F4229;"></span>
            <span class="text-sm font-medium text-gray-700">
                Grupo <strong>{{ $alumno->grupo->clave ?? '—' }}</strong>
            </span>
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
                    {{ $cargas->count() }} {{ $cargas->count() === 1 ? 'materia' : 'materias' }}
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
                            <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                            <p class="text-sm text-gray-500 max-w-xs mx-auto">No hay horario asignado. El grupo aún no tiene carga académica registrada.</p>
                        </div>
                    </div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Materia</th>
                            <th class="px-6 py-3">Profesor</th>
                            <th class="px-6 py-3">Horario</th>
                            <th class="px-6 py-3">Aula virtual</th>
                            <th class="px-6 py-3">Período</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($cargas as $c)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">
                            <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                {{ $c->materia->nombre ?? '—' }}
                                <span class="block text-xs text-gray-400 font-normal">{{ $c->materia->clave ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                {{ $c->profesor->nombre ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $c->horario_formateado ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.75 10.5l4.72-2.72a.75.75 0 011.03.6v7.24a.75.75 0 01-1.03.6L15.75 14
                                                 M4.5 6.75h9a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5h-9a1.5 1.5 0 01-1.5-1.5v-7.5a1.5 1.5 0 011.5-1.5z"/>
                                    </svg>
                                    {{ $c->aula ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs whitespace-nowrap">
                                {{ $c->periodo->nombre ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        {{-- Vista semanal --}}
        @php
            $diasLabel = ['lunes'=>'Lun','martes'=>'Mar','miercoles'=>'Mié','jueves'=>'Jue','viernes'=>'Vie','sabado'=>'Sáb','domingo'=>'Dom'];
            $conHorario = $cargas->filter(fn($c) => $c->dia_semana && $c->hora_inicio && $c->hora_fin);
            $toMin = fn($t) => ((int) explode(':', $t)[0]) * 60 + (int) explode(':', $t)[1];

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
            $colorPorMateria = [];
            foreach ($conHorario as $c) {
                if (!isset($colorPorMateria[$c->materia_id])) {
                    $colorPorMateria[$c->materia_id] = $paleta[count($colorPorMateria) % count($paleta)];
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
                                        $color = $colorPorMateria[$c->materia_id];
                                    @endphp
                                    <div class="absolute left-1 right-1 rounded-lg px-2 py-1 overflow-hidden"
                                         style="top: {{ $top }}px; height: {{ $alto }}px; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-left: 3px solid {{ $color['borde'] }};"
                                         title="{{ $c->materia->nombre }} · {{ $c->hora_inicio }} - {{ $c->hora_fin }}">
                                        <p class="text-[11px] font-bold leading-tight truncate">{{ $c->materia->nombre }}</p>
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
