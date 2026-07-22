@extends('layouts.app')

@section('title', 'Aclaraciones de calificación | UICM')

@section('content')
@php
    $totalAclaraciones = $aclaraciones->count();
    $conteoPorEstado = [
        'pendiente' => $aclaraciones->where('estado', 'pendiente')->count(),
        'aprobada'  => $aclaraciones->where('estado', 'aprobada')->count(),
        'rechazada' => $aclaraciones->where('estado', 'rechazada')->count(),
        'todas'     => $totalAclaraciones,
    ];
@endphp
<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{
             busqueda: '',
             filtroTipo: '',
             filtroEstado: 'pendiente',
             contadorVisible: {{ $conteoPorEstado['pendiente'] }},
             filtrar() {
                 this.$nextTick(() => {
                     const cards = this.$refs.lista.querySelectorAll('[data-alumno]');
                     let visibles = 0;
                     cards.forEach(c => {
                         const texto = this.busqueda.toLowerCase();
                         const pasaBusq   = !texto
                             || c.dataset.alumno.includes(texto)
                             || c.dataset.profesor.includes(texto)
                             || c.dataset.materia.includes(texto);
                         const pasaTipo   = !this.filtroTipo   || c.dataset.tipo === this.filtroTipo;
                         const pasaEstado = !this.filtroEstado || c.dataset.estado === this.filtroEstado;
                         const mostrar = pasaBusq && pasaTipo && pasaEstado;
                         c.style.display = mostrar ? '' : 'none';
                         if (mostrar) visibles++;
                     });
                     this.contadorVisible = visibles;
                     this.$refs.sinResultados.style.display = (visibles === 0 && {{ $totalAclaraciones }} > 0) ? '' : 'none';
                 });
             }
         }"
         x-init="filtrar()">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Coordinación Académica
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Aclaraciones de calificación</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Tarjetas de resumen (también filtran por estado) --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <button type="button" @click="filtroEstado = 'pendiente'; filtrar()"
                    :class="filtroEstado === 'pendiente' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pendientes</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $conteoPorEstado['pendiente'] }}</p>
            </button>
            <button type="button" @click="filtroEstado = 'aprobada'; filtrar()"
                    :class="filtroEstado === 'aprobada' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Aprobadas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $conteoPorEstado['aprobada'] }}</p>
            </button>
            <button type="button" @click="filtroEstado = 'rechazada'; filtrar()"
                    :class="filtroEstado === 'rechazada' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #dc2626;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Rechazadas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #dc2626;">{{ $conteoPorEstado['rechazada'] }}</p>
            </button>
            <button type="button" @click="filtroEstado = ''; filtrar()"
                    :class="filtroEstado === '' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #6B7280;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Todas</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $conteoPorEstado['todas'] }}</p>
            </button>
        </div>

        {{-- Buscador y filtro por tipo --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" x-model="busqueda" @input="filtrar()"
                       placeholder="Buscar por alumno, matrícula, profesor o materia…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div class="relative sm:w-52">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <select x-model="filtroTipo" @change="filtrar()"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los tipos</option>
                    <option value="final">Final</option>
                    <option value="extraordinario">Extraordinario</option>
                </select>
            </div>
        </div>

        <div class="space-y-6" x-ref="lista">

            @if ($aclaraciones->isEmpty())
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin aclaraciones</h3>
                    <p class="text-sm text-gray-500 max-w-xs">No hay solicitudes de aclaración registradas.</p>
                </div>
            </div>
            @else

            <div x-ref="sinResultados" style="display: none;" class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin aclaraciones</h3>
                    <p class="text-sm text-gray-500 max-w-xs">Ninguna aclaración coincide con la búsqueda o los filtros.</p>
                </div>
            </div>

            @foreach ($aclaraciones as $aclaracion)
            <div class="bg-white rounded-2xl shadow-md overflow-hidden"
                 data-alumno="{{ strtolower($aclaracion->alumno->nombre_completo . ' ' . $aclaracion->alumno->matricula) }}"
                 data-profesor="{{ strtolower($aclaracion->profesor->nombre ?? '') }}"
                 data-materia="{{ strtolower($aclaracion->cargaAcademica->materia->nombre ?? '') }}"
                 data-tipo="{{ $aclaracion->tipo }}"
                 data-estado="{{ $aclaracion->estado }}"
                 x-data="{ rechazando: false }">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm">
                            {{ $aclaracion->alumno->nombre_completo }}
                            <span class="text-xs text-gray-400">({{ $aclaracion->alumno->matricula }})</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $aclaracion->cargaAcademica->materia->nombre ?? '—' }}
                            &nbsp;·&nbsp; Grupo {{ $aclaracion->cargaAcademica->grupo->clave ?? '—' }}
                            &nbsp;·&nbsp; Tipo: <strong>{{ ucfirst($aclaracion->tipo) }}</strong>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Solicitado por: {{ $aclaracion->profesor->nombre ?? '—' }}
                            el {{ $aclaracion->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @php
                        $colorEstado = ['pendiente' => '#EFAD5A', 'aprobada' => '#0F4229'][$aclaracion->estado] ?? '#dc2626';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                          style="background-color: {{ $colorEstado }};">
                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                        {{ ucfirst($aclaracion->estado) }}
                    </span>
                </div>

                <div class="px-6 py-4">
                    <p class="text-sm text-gray-700">
                        Calificación propuesta:
                        <strong class="{{ $aclaracion->calificacion_propuesta >= 7.0 ? 'text-green-700' : 'text-red-600' }}">
                            {{ number_format($aclaracion->calificacion_propuesta, 1) }}
                        </strong>
                    </p>
                    <p class="text-sm text-gray-500 mt-2"><strong>Motivo:</strong> {{ $aclaracion->motivo }}</p>

                    @if ($aclaracion->estado === 'rechazada' && $aclaracion->motivo_rechazo)
                        <p class="text-sm text-red-600 mt-2"><strong>Motivo de rechazo:</strong> {{ $aclaracion->motivo_rechazo }}</p>
                    @endif
                </div>

                @if ($aclaracion->estado === 'pendiente')
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-2" x-show="!rechazando">
                    <form method="POST" action="{{ route('admin.aclaraciones.aprobar', $aclaracion) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white"
                                style="background-color: #0F4229;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Aprobar
                        </button>
                    </form>
                    <button type="button" @click="rechazando = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white"
                            style="background-color: #dc2626;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Rechazar
                    </button>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50" x-show="rechazando" x-cloak>
                    <form method="POST" action="{{ route('admin.aclaraciones.rechazar', $aclaracion) }}" class="flex flex-col gap-2">
                        @csrf
                        <label class="text-xs font-semibold text-gray-600">Motivo del rechazo</label>
                        <textarea name="motivo_rechazo" required rows="2"
                                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none"></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="rechazando = false"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600">Cancelar</button>
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl text-xs font-bold bg-red-600 text-white">Confirmar rechazo</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            @endforeach

            @endif

        </div>

    </div>
</section>
@endsection
