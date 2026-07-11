@extends('layouts.app')

@section('title', 'Mis materias | UICM')

@section('content')
@php
    $periodosDisponibles = $cargas->pluck('periodo')->filter()->unique('id')->sortByDesc('id');
@endphp
<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{
             filtroPeriodo: '',
             filtroEstado: '',
             busqueda: '',
             filtrar() {
                 this.$nextTick(() => {
                     const filas = this.$refs.tbody.querySelectorAll('[data-periodo]');
                     const texto = this.busqueda.trim().toLowerCase();
                     let visibles = 0;
                     filas.forEach(f => {
                         const coincidePeriodo = !this.filtroPeriodo || f.dataset.periodo === this.filtroPeriodo;
                         const coincideEstado  = !this.filtroEstado || f.dataset.estado === this.filtroEstado;
                         const coincideTexto   = !texto || f.dataset.busqueda.includes(texto);
                         const mostrar = coincidePeriodo && coincideEstado && coincideTexto;
                         f.style.display = mostrar ? '' : 'none';
                         if (mostrar) visibles++;
                     });
                     if (this.$refs.sinResultados) {
                         this.$refs.sinResultados.style.display = visibles === 0 ? '' : 'none';
                     }
                 });
             }
         }"
         x-init="filtrar()">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Profesor</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Mis materias asignadas</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        @if ($cargas->isEmpty())
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                                     C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                                     C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                                     C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin materias asignadas</h3>
                    <p class="text-sm text-gray-500 max-w-xs">No tienes materias asignadas por el momento.</p>
                </div>
            </div>
        @else
            {{-- Tarjetas resumen (también filtran la lista) --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
                <button type="button" @click="filtroEstado = ''; filtrar()"
                        :class="filtroEstado === '' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                        class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                        style="border-color: #6B7280;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total</p>
                    <p class="text-2xl font-extrabold mt-1 text-gray-600">{{ $conteo['total'] }}</p>
                </button>
                <button type="button" @click="filtroEstado = (filtroEstado === 'aprobado' ? '' : 'aprobado'); filtrar()"
                        :class="filtroEstado === 'aprobado' ? 'ring-2' : 'hover:shadow-md'"
                        class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                        style="border-color: #0F4229;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Aprobado</p>
                    <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $conteo['aprobado'] }}</p>
                </button>
                <button type="button" @click="filtroEstado = (filtroEstado === 'pendiente' ? '' : 'pendiente'); filtrar()"
                        :class="filtroEstado === 'pendiente' ? 'ring-2' : 'hover:shadow-md'"
                        class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                        style="border-color: #EFAD5A;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">En revisión</p>
                    <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $conteo['pendiente'] }}</p>
                </button>
                <button type="button" @click="filtroEstado = (filtroEstado === 'sin_enviar' ? '' : 'sin_enviar'); filtrar()"
                        :class="filtroEstado === 'sin_enviar' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                        class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                        style="border-color: #9ca3af;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Sin enviar</p>
                    <p class="text-2xl font-extrabold mt-1 text-gray-400">{{ $conteo['sin_enviar'] }}</p>
                </button>
                <button type="button" @click="filtroEstado = (filtroEstado === 'rechazado' ? '' : 'rechazado'); filtrar()"
                        :class="filtroEstado === 'rechazado' ? 'ring-2 ring-red-400' : 'hover:shadow-md'"
                        class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                        style="border-color: #dc2626;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Rechazado</p>
                    <p class="text-2xl font-extrabold mt-1 text-red-600">{{ $conteo['rechazado'] }}</p>
                </button>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" x-model="busqueda" @input="filtrar()"
                           placeholder="Buscar por materia o grupo…"
                           class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>
                <div class="relative sm:w-72">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <select x-model="filtroPeriodo" @change="filtrar()"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">Todos los períodos</option>
                        @foreach($periodosDisponibles as $periodo)
                        <option value="{{ $periodo->id }}">{{ $periodo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Grupo</th>
                                <th class="px-6 py-3">Materia</th>
                                <th class="px-6 py-3">Período</th>
                                <th class="px-6 py-3">Horario</th>
                                <th class="px-6 py-3">Aula</th>
                                <th class="px-6 py-3 text-center">Revisión</th>
                                <th class="px-6 py-3 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" x-ref="tbody">
                            <tr x-ref="sinResultados" style="display: none;">
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                                    Ninguna materia coincide con los filtros seleccionados.
                                </td>
                            </tr>
                            @foreach ($cargas as $carga)
                            @php
                                    $estadoBadge = match($carga->estado_revision) {
                                        'pendiente' => ['#EFAD5A', 'En revisión'],
                                        'aprobado'  => ['#0F4229', 'Aprobado'],
                                        'rechazado' => ['#dc2626', 'Rechazado'],
                                        default     => ['#9ca3af', 'Sin enviar'],
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50" data-periodo="{{ $carga->periodo_id }}"
                                    data-estado="{{ $carga->estado_revision ?? 'sin_enviar' }}"
                                    data-busqueda="{{ strtolower(($carga->grupo->clave ?? '').' '.($carga->materia->nombre ?? '').' '.($carga->materia->clave ?? '')) }}">
                                    <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-700">
                                        {{ $carga->grupo->clave ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $carga->materia->nombre ?? '—' }}
                                        <span class="block text-xs text-gray-400">{{ $carga->materia->clave ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">
                                        {{ $carga->periodo->nombre ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">
                                        {{ $carga->horario ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">
                                        {{ $carga->aula ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                              style="background-color: {{ $estadoBadge[0] }};">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                            {{ $estadoBadge[1] }}
                                        </span>
                                        @if ($carga->estado_revision === 'rechazado' && $carga->motivo_rechazo)
                                            <span class="block text-xs text-red-500 mt-1 max-w-[160px]">{{ $carga->motivo_rechazo }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('profesor.calificaciones.capturar', $carga->id) }}"
                                           class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-white whitespace-nowrap"
                                           style="background-color: #0F4229;"
                                           onmouseover="this.style.backgroundColor='#0a2e1c'"
                                           onmouseout="this.style.backgroundColor='#0F4229'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                            </svg>
                                            Capturar calificaciones
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>

</section>
@endsection
