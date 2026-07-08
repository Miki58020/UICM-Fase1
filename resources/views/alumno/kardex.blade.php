@extends('layouts.app')

@section('title', 'Historial Académico | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-10 px-4"
         x-data="{
             busqueda: '',
             filtroCuatrimestre: '',
             filtrar() {
                 this.$nextTick(() => {
                     const q = this.busqueda.toLowerCase();
                     let bloquesVisibles = 0;
                     this.$refs.lista.querySelectorAll('[data-cuatrimestre]').forEach(bloque => {
                         const pasaCuatri = !this.filtroCuatrimestre || bloque.dataset.cuatrimestre === this.filtroCuatrimestre;
                         let filasVisibles = 0;
                         bloque.querySelectorAll('tr[data-materia]').forEach(fila => {
                             const ok = !q || fila.dataset.materia.includes(q);
                             fila.style.display = ok ? '' : 'none';
                             if (ok) filasVisibles++;
                         });
                         const mostrar = pasaCuatri && filasVisibles > 0;
                         bloque.style.display = mostrar ? '' : 'none';
                         if (mostrar) bloquesVisibles++;
                     });
                     this.$refs.sinResultados.style.display = bloquesVisibles === 0 ? '' : 'none';
                 });
             }
         }"
         x-init="filtrar()">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        {{-- Encabezado --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Alumno</p>
                <h1 class="text-2xl font-extrabold text-gray-900">Historial académico</h1>
                <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
            </div>
            <a href="{{ route('alumno.kardex.imprimir') }}" target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white
                      shadow-sm transition-colors duration-150 self-start sm:self-auto flex-shrink-0"
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
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Promedio</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">
                    {{ $promedioGeneral !== null ? number_format($promedioGeneral, 1) : '—' }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #16a34a;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Aprobadas</p>
                <p class="text-2xl font-extrabold mt-1 text-green-700">{{ $aprobadas }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #dc2626;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Reprobadas</p>
                <p class="text-2xl font-extrabold mt-1 text-red-600">{{ $reprobadas }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pendientes</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">{{ $pendientes }}</p>
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
            {{-- Nota informativa --}}
            <div class="mb-6 flex items-start gap-2 text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2.5">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>
                    Calificación mínima aprobatoria: <strong>7.0</strong>.
                    Solo se muestran materias aprobadas por control escolar.
                    Para el documento oficial, usa el botón "Generar Kárdex".
                </span>
            </div>

            {{-- Buscador y filtro por cuatrimestre --}}
            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" x-model="busqueda" @input="filtrar()"
                           placeholder="Buscar materia por nombre o clave…"
                           class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>
                <div class="relative sm:w-56">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    <select x-model="filtroCuatrimestre" @change="filtrar()"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">Todos los cuatrimestres</option>
                        @foreach($porCuatrimestre as $numCuatri => $items)
                        <option value="{{ $numCuatri }}">{{ $numCuatri > 0 ? $numCuatri.'° Cuatrimestre' : 'Sin cuatrimestre' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-5" x-ref="lista">

                <div x-ref="sinResultados" style="display: none;" class="bg-white rounded-2xl shadow-sm px-6 py-12 text-center text-sm text-gray-400">
                    Ninguna materia coincide con la búsqueda o los filtros.
                </div>

                @foreach($porCuatrimestre as $numCuatri => $items)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden" data-cuatrimestre="{{ $numCuatri }}">
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
                                <tr class="hover:bg-gray-50" data-materia="{{ strtolower($m['carga']->materia->nombre.' '.$m['carga']->materia->clave) }}">
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
        @endif

    </div>
</section>
@endsection
