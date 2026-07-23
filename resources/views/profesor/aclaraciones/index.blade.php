@extends('layouts.app')

@section('title', 'Aclaraciones | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{
             filtroEstado: '',
             filtroPeriodo: '',
             filtrar() {
                 this.$nextTick(() => {
                     const tarjetas = this.$refs.lista.querySelectorAll('[data-estado]');
                     let visibles = 0;
                     tarjetas.forEach(t => {
                         const pasaEstado  = !this.filtroEstado || t.dataset.estado === this.filtroEstado;
                         const pasaPeriodo = !this.filtroPeriodo || t.dataset.periodo === this.filtroPeriodo;
                         const mostrar = pasaEstado && pasaPeriodo;
                         t.style.display = mostrar ? '' : 'none';
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
            <h1 class="text-2xl font-extrabold text-gray-900">Aclaraciones</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        @php
            $totalPendientes = $aclaraciones->where('estado', 'pendiente')->count();
            $totalAprobadas  = $aclaraciones->where('estado', 'aprobada')->count();
            $totalRechazadas = $aclaraciones->where('estado', 'rechazada')->count();
            $periodosDisponibles = $aclaraciones->pluck('cargaAcademica.periodo')->filter()->unique('id')->sortByDesc('id');
        @endphp

        {{-- Tarjetas resumen (también filtran la lista) --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <button type="button" @click="filtroEstado = ''; filtrar()"
                    :class="filtroEstado === '' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #6B7280;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Todas</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-600">{{ $aclaraciones->count() }}</p>
            </button>
            <button type="button" @click="filtroEstado = (filtroEstado === 'pendiente' ? '' : 'pendiente'); filtrar()"
                    :class="filtroEstado === 'pendiente' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">En revisión</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $totalPendientes }}</p>
            </button>
            <button type="button" @click="filtroEstado = (filtroEstado === 'aprobada' ? '' : 'aprobada'); filtrar()"
                    :class="filtroEstado === 'aprobada' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Aprobadas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $totalAprobadas }}</p>
            </button>
            <button type="button" @click="filtroEstado = (filtroEstado === 'rechazada' ? '' : 'rechazada'); filtrar()"
                    :class="filtroEstado === 'rechazada' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #dc2626;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Rechazadas</p>
                <p class="text-2xl font-extrabold mt-1 text-red-600">{{ $totalRechazadas }}</p>
            </button>
        </div>

        @if ($periodosDisponibles->count() > 1)
        <div class="relative sm:w-72 mb-6">
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
                <option value="{{ $periodo->id }}">{{ $periodo->label ?? $periodo->nombre }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="space-y-4" x-ref="lista">

            @if ($aclaraciones->isNotEmpty())
            <div x-ref="sinResultados" style="display: none;" class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                    <p class="text-sm text-gray-500 max-w-xs">Ninguna aclaración coincide con el filtro seleccionado.</p>
                </div>
            </div>
            @endif

            @forelse ($aclaraciones as $aclaracion)
            @php
                $resuelta = $aclaracion->estado !== 'pendiente';
                $paso3 = match($aclaracion->estado) {
                    'aprobada'  => ['color' => '#0F4229', 'texto' => 'Aprobada',  'clase' => 'text-uicm-green', 'icono' => 'check'],
                    'rechazada' => ['color' => '#dc2626', 'texto' => 'Rechazada', 'clase' => 'text-red-600',    'icono' => 'x'],
                    default     => null,
                };
            @endphp
            <div class="bg-white rounded-2xl shadow-md overflow-hidden" data-estado="{{ $aclaracion->estado }}" data-periodo="{{ $aclaracion->cargaAcademica->periodo_id ?? '' }}">
                <div class="h-1.5 w-full" style="background-color: {{ $paso3['color'] ?? '#EFAD5A' }};"></div>

                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-start justify-between gap-4">

                    {{-- Datos de la aclaración --}}
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $aclaracion->alumno->nombre_completo ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $aclaracion->cargaAcademica->materia->nombre ?? '—' }}
                            &middot; {{ $aclaracion->cargaAcademica->grupo->clave ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-600 mt-2">
                            <span class="font-semibold">{{ ucfirst($aclaracion->tipo) }}</span> propuesto:
                            <span class="font-mono font-bold">{{ number_format($aclaracion->calificacion_propuesta, 1) }}</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1 italic">"{{ $aclaracion->motivo }}"</p>
                    </div>

                    {{-- Cadena de proceso --}}
                    <div class="flex items-center gap-2 flex-shrink-0 self-start">
                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shadow-sm"
                                 style="background-color: #0F4229;">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold mt-1 text-uicm-green whitespace-nowrap">Enviada</p>
                        </div>

                        <div class="w-8 h-0.5 mb-4" style="background-color: #0F4229;"></div>

                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shadow-sm {{ !$resuelta ? 'ring-4 ring-orange-200' : '' }}"
                                 style="background-color: {{ $resuelta ? '#0F4229' : '#EFAD5A' }};">
                                @if($resuelta)
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    <div class="w-2 h-2 rounded-full bg-white"></div>
                                @endif
                            </div>
                            <p class="text-xs font-semibold mt-1 {{ $resuelta ? 'text-uicm-green' : 'text-orange-500' }} whitespace-nowrap">En revisión</p>
                        </div>

                        <div class="w-8 h-0.5 mb-4" style="background-color: {{ $resuelta ? $paso3['color'] : '#E5E7EB' }};"></div>

                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shadow-sm {{ !$resuelta ? 'opacity-40' : '' }}"
                                 style="background-color: {{ $resuelta ? $paso3['color'] : '#D1D5DB' }};">
                                @if($resuelta && $paso3['icono'] === 'check')
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @elseif($resuelta && $paso3['icono'] === 'x')
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                @else
                                    <div class="w-2 h-2 rounded-full bg-white"></div>
                                @endif
                            </div>
                            <p class="text-xs font-semibold mt-1 {{ $resuelta ? $paso3['clase'] : 'text-gray-400' }} whitespace-nowrap">{{ $resuelta ? $paso3['texto'] : 'Resultado' }}</p>
                        </div>
                    </div>

                </div>

                @if($aclaracion->estado === 'rechazada' && $aclaracion->motivo_rechazo)
                <div class="px-6 py-3 border-t border-gray-100 bg-red-50">
                    <p class="text-xs text-red-600"><span class="font-semibold">Motivo del rechazo:</span> {{ $aclaracion->motivo_rechazo }}</p>
                </div>
                @endif
            </div>
            @empty
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4v-4z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin aclaraciones</h3>
                    <p class="text-sm text-gray-500 max-w-xs">Aún no has solicitado ninguna aclaración de calificación.</p>
                </div>
            </div>
            @endforelse
        </div>

    </div>
</section>
@endsection
