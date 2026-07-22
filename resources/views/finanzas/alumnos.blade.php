@extends('layouts.app')

@section('title', 'Alumnos al corriente | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{
             busqueda: '',
             filtroPrograma: '',
             filtroEstado: '',
             filtrar() {
                 this.$nextTick(() => {
                     const filas = this.$refs.lista.querySelectorAll('[data-nombre]');
                     let visibles = 0;
                     filas.forEach(f => {
                         const q = this.busqueda.toLowerCase();
                         const pasaBusq    = !q || f.dataset.nombre.includes(q) || f.dataset.matricula.includes(q);
                         const pasaPrograma = !this.filtroPrograma || f.dataset.programa === this.filtroPrograma;
                         const pasaEstado   = !this.filtroEstado || f.dataset.estado === this.filtroEstado;
                         const mostrar = pasaBusq && pasaPrograma && pasaEstado;
                         f.style.display = mostrar ? '' : 'none';
                         if (mostrar) visibles++;
                     });
                     this.$refs.sinResultados.style.display = visibles === 0 ? '' : 'none';
                     this.$refs.contador.textContent = visibles + ' alumno' + (visibles !== 1 ? 's' : '');
                 });
             }
         }"
         x-init="filtrar()">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Finanzas</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Alumnos: al corriente / atrasados</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Tarjetas (también filtran) --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <button type="button" @click="filtroEstado = 'atrasado'; filtrar()"
                    :class="filtroEstado === 'atrasado' ? 'ring-2 ring-red-500' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 border-red-500 text-left w-full transition-shadow duration-150">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Atrasados</p>
                <p class="text-2xl font-extrabold mt-1 text-red-600">{{ $totalAtrasados }}</p>
            </button>
            <button type="button" @click="filtroEstado = 'corriente'; filtrar()"
                    :class="filtroEstado === 'corriente' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Al corriente</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $totalAlCorriente }}</p>
            </button>
            <button type="button" @click="filtroEstado = ''; filtrar()"
                    :class="filtroEstado === '' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #6B7280;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Todos</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $alumnos->count() }}</p>
            </button>
        </div>

        {{-- Buscador y filtro por programa --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" x-model="busqueda" @input="filtrar()"
                       placeholder="Buscar por nombre o matrícula…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div class="relative sm:w-60">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <select x-model="filtroPrograma" @change="filtrar()"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los programas</option>
                    @foreach ($programas as $programa)
                        <option value="{{ $programa->id }}">{{ $programa->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Alumnos activos</h2>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green" x-ref="contador">{{ $alumnos->count() }} alumnos</span>
            </div>

            @if ($alumnos->isEmpty())
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5a4 4 0 11-8 0 4 4 0 018 0zm6 3a4 4 0 10-8 0"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin alumnos registrados</h3>
                    <p class="text-sm text-gray-500 max-w-xs">No hay alumnos activos en el sistema.</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <div class="min-w-[700px]" x-ref="lista">
                    <div class="grid grid-cols-[1fr_2.2fr_2fr_1.3fr_1.3fr_1.3fr] gap-x-4 px-6 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <div>Matrícula</div>
                        <div>Nombre</div>
                        <div>Programa</div>
                        <div class="text-center">Estado</div>
                        <div class="text-center">Pagos atrasados</div>
                        <div>Monto atrasado</div>
                    </div>
                    <div x-ref="sinResultados" style="display: none;" class="px-6 py-12 flex flex-col items-center text-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                        <p class="text-sm text-gray-500 max-w-xs">Ningún alumno coincide con la búsqueda o los filtros.</p>
                    </div>
                    <div class="divide-y divide-gray-100 text-sm">
                        @foreach ($alumnos as $alumno)
                        <div class="grid grid-cols-[1fr_2.2fr_2fr_1.3fr_1.3fr_1.3fr] gap-x-4 px-6 py-4 items-center hover:bg-gray-50"
                             data-nombre="{{ strtolower($alumno->nombre_completo) }}"
                             data-matricula="{{ strtolower($alumno->matricula) }}"
                             data-programa="{{ $alumno->programa_id }}"
                             data-estado="{{ $alumno->alCorriente ? 'corriente' : 'atrasado' }}">
                            <div class="text-sm text-gray-600 truncate">{{ $alumno->matricula }}</div>
                            <div class="font-medium text-gray-800 truncate">{{ $alumno->nombre_completo }}</div>
                            <div class="text-gray-500 truncate">{{ $alumno->programa?->nombre ?? '—' }}</div>
                            <div class="text-center">
                                @if ($alumno->alCorriente)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Al corriente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #dc2626;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Atrasado
                                    </span>
                                @endif
                            </div>
                            <div class="text-center">
                                @if ($alumno->cantidadAtrasados)
                                    <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 px-1.5 text-xs font-bold rounded-full text-white" style="background-color: #dc2626;">
                                        {{ $alumno->cantidadAtrasados }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </div>
                            <div class="font-bold truncate {{ $alumno->montoAtrasado > 0 ? 'text-red-600' : 'text-gray-300' }}">
                                {{ $alumno->montoAtrasado > 0 ? '$'.number_format($alumno->montoAtrasado, 2) : '—' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection
