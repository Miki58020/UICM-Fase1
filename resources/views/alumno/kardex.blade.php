@extends('layouts.app')

@section('title', 'Historial Académico | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-10 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        {{-- Encabezado --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <a href="{{ route('alumno.dashboard') }}"
                   class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4 w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Mi portal
                </a>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Alumno</p>
                <h1 class="text-2xl font-extrabold text-gray-900">Historial académico</h1>
                <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
            </div>
            <a href="{{ route('alumno.kardex.imprimir') }}" target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white
                      transition-colors duration-150 self-start sm:mt-10 flex-shrink-0"
               style="background-color: #0F4229;"
               onmouseover="this.style.backgroundColor='#0a2e1c'"
               onmouseout="this.style.backgroundColor='#0F4229'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2
                             m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5
                             a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Generar Kárdex
            </a>
        </div>

        {{-- Tarjetas resumen --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-sm px-4 py-4 text-center border-t-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Promedio</p>
                <p class="text-2xl font-extrabold" style="color: #0F4229;">
                    {{ $promedioGeneral !== null ? number_format($promedioGeneral, 1) : '—' }}
                </p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm px-4 py-4 text-center border-t-4" style="border-color: #16a34a;">
                <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Aprobadas</p>
                <p class="text-2xl font-extrabold text-green-700">{{ $aprobadas }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm px-4 py-4 text-center border-t-4" style="border-color: #dc2626;">
                <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Reprobadas</p>
                <p class="text-2xl font-extrabold text-red-600">{{ $reprobadas }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm px-4 py-4 text-center border-t-4" style="border-color: #D4AF37;">
                <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Pendientes</p>
                <p class="text-2xl font-extrabold" style="color: #D4AF37;">{{ $pendientes }}</p>
            </div>
        </div>

        {{-- Barra de créditos --}}
        @if($totalCreditosPrograma > 0)
        @php $pct = min(100, round(($alumno->creditos_acumulados / $totalCreditosPrograma) * 100)); @endphp
        <div class="bg-white rounded-2xl shadow-sm px-6 py-5 mb-8">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-700">Avance de créditos</p>
                <p class="text-sm font-bold" style="color: #0F4229;">
                    {{ $alumno->creditos_acumulados }} / {{ $totalCreditosPrograma }}
                    <span class="text-xs font-normal text-gray-400 ml-1">{{ $pct }}%</span>
                </p>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5">
                <div class="h-2.5 rounded-full" style="width: {{ $pct }}%; background-color: #0F4229;"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1.5">
                Programa: {{ $alumno->programa->nombre ?? '—' }} · {{ $alumno->programa->duracion_cuatrimestres ?? '—' }} cuatrimestres
            </p>
        </div>
        @endif

        {{-- Calificaciones por cuatrimestre --}}
        @if($porCuatrimestre->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm px-6 py-12 text-center text-sm text-gray-400">
                Aún no hay calificaciones registradas.
            </div>
        @else
            <div class="space-y-5">
                @foreach($porCuatrimestre as $numCuatri => $items)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="h-1 w-full" style="background-color: #0F4229;"></div>
                    <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-sm font-extrabold" style="color: #0F4229;">
                            {{ $numCuatri > 0 ? $numCuatri.'° Cuatrimestre' : 'Sin cuatrimestre asignado' }}
                        </h2>
                        <span class="text-xs text-gray-400">
                            {{ $items->first()['carga']->periodo->label ?? '' }}
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3">Materia</th>
                                    <th class="px-4 py-3 text-center">Créditos</th>
                                    <th class="px-4 py-3 text-center">Final</th>
                                    <th class="px-4 py-3 text-center">Extraord.</th>
                                    <th class="px-4 py-3 text-center">Cal. Final</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($items as $m)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 font-medium text-gray-800 whitespace-nowrap">
                                        {{ $m['carga']->materia->nombre }}
                                        <span class="block text-xs text-gray-400 font-normal">{{ $m['carga']->materia->clave }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold text-white"
                                              style="background-color: #D4AF37;">
                                            {{ $m['carga']->materia->creditos }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold">
                                        @if($m['final'])
                                            <span class="{{ $m['final']->calificacion >= 7.0 ? 'text-green-700' : 'text-red-600' }}">
                                                {{ number_format($m['final']->calificacion, 1) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold">
                                        @if($m['ex'])
                                            <span class="{{ $m['ex']->calificacion >= 7.0 ? 'text-green-700' : 'text-red-600' }}">
                                                {{ number_format($m['ex']->calificacion, 1) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold">
                                        @if($m['calFinal'] !== null)
                                            <span class="{{ $m['aprobado'] ? 'text-green-700' : 'text-red-600' }}">
                                                {{ number_format($m['calFinal'], 1) }}
                                                @if($m['ex'])
                                                    <span class="block text-xs font-normal text-gray-400">(extra)</span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($m['calFinal'] === null)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                                  style="background-color: #EFAD5A;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                                Pendiente
                                            </span>
                                        @elseif($m['aprobado'])
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                                  style="background-color: #0F4229;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                                Aprobada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-white bg-red-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                                Reprobada
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>

            <p class="mt-6 text-xs text-gray-400 text-center">
                Calificación mínima aprobatoria: <strong>7.0</strong>.
                Solo se muestran materias aprobadas por control escolar.
                Para el documento oficial, usa el botón "Generar Kárdex".
            </p>
        @endif

    </div>
</section>
@endsection
