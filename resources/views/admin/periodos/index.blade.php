@extends('layouts.app')

@section('title', 'Cuatrimestres | UICM')

@section('content')

@php
$nivelCfg = [
    'licenciatura' => ['label' => 'Licenciaturas', 'color' => '#0F4229'],
    'maestria'     => ['label' => 'Maestrías',     'color' => '#D4AF37'],
    'doctorado'    => ['label' => 'Doctorado',     'color' => '#9ca3af'],
];
$periodoInicialId = session('periodos_tab_periodo', $periodos->first()?->id);
@endphp

<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{
             tab: '{{ session('periodos_tab', 'cuatrimestres') }}',
             periodoId: {{ $periodoInicialId ?? 'null' }}
         }">
<div class="container mx-auto px-4 lg:px-12 max-w-7xl">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Coordinación Académica
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Cuatrimestres</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>
        {{-- Botón contextual --}}
        <div x-show="tab === 'cuatrimestres'" x-cloak>
            <button onclick="document.getElementById('modal-crear').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200"
                    style="background-color: #0F4229;"
                    onmouseover="this.style.backgroundColor='#0a2e1c'"
                    onmouseout="this.style.backgroundColor='#0F4229'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo cuatrimestre
            </button>
        </div>
        <div x-show="tab === 'carreras'" x-cloak>
            <button @click="document.getElementById('modal-agregar-' + periodoId).classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200"
                    style="background-color: #0F4229;"
                    onmouseover="this.style.backgroundColor='#0a2e1c'"
                    onmouseout="this.style.backgroundColor='#0F4229'"
                    x-show="periodoId !== null">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar carrera
            </button>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-white rounded-2xl shadow-sm p-1 mb-6 w-full sm:w-fit">
        <button @click="tab = 'cuatrimestres'"
                :class="tab === 'cuatrimestres' ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                :style="tab === 'cuatrimestres' ? 'background-color:#0F4229' : ''"
                class="flex-1 sm:flex-none px-3 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150">
            Cuatrimestres
        </button>
        <button @click="tab = 'inscripciones'"
                :class="tab === 'inscripciones' ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                :style="tab === 'inscripciones' ? 'background-color:#0F4229' : ''"
                class="flex-1 sm:flex-none px-3 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150">
            Inscripciones
        </button>
        <button @click="tab = 'carreras'"
                :class="tab === 'carreras' ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                :style="tab === 'carreras' ? 'background-color:#0F4229' : ''"
                class="flex-1 sm:flex-none px-3 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150">
            Carreras
        </button>
    </div>

    {{-- ══ TAB: CUATRIMESTRES ══ --}}
    <div x-show="tab === 'cuatrimestres'" x-cloak
         x-data="{
             busqueda: '',
             filtrar() {
                 this.$nextTick(() => {
                     const filas = this.$refs.tbodyCuatrimestres.querySelectorAll('tr[data-nombre]');
                     let visibles = 0;
                     filas.forEach(f => {
                         const texto = this.busqueda.toLowerCase();
                         const pasa = !texto || f.dataset.nombre.includes(texto) || f.dataset.label.includes(texto);
                         f.style.display = pasa ? '' : 'none';
                         if (pasa) visibles++;
                     });
                     this.$refs.sinResultadosCuatrimestres.style.display = visibles === 0 ? '' : 'none';
                     this.$refs.contadorCuatrimestres.textContent = visibles + ' registro' + (visibles !== 1 ? 's' : '');
                 });
             }
         }">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $periodos->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Con clases</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">{{ $periodos->whereNotNull('fecha_inicio_clases')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Activo</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $periodos->where('estado','activo')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #9ca3af;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Cerrados</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $periodos->where('estado','cerrado')->count() }}</p>
            </div>
        </div>

        <div class="relative mb-6">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input type="text" x-model="busqueda" @input="filtrar()"
                   placeholder="Buscar por clave o nombre…"
                   class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                   onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                   onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
        </div>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Listado de cuatrimestres</h2>
                <span class="text-xs text-gray-400" x-ref="contadorCuatrimestres">{{ $periodos->count() }} registro{{ $periodos->count() !== 1 ? 's' : '' }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Clave</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Inicio de clases</th>
                            <th class="px-6 py-3">Fin de clases</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tbodyCuatrimestres">
                        @forelse($periodos as $periodo)
                        <tr class="hover:bg-gray-50 transition-colors duration-100"
                            data-periodo-id="{{ $periodo->id }}"
                            data-nombre="{{ strtolower($periodo->nombre) }}"
                            data-label="{{ strtolower($periodo->label) }}"
                            data-inicio-reg="{{ $periodo->fecha_inicio_registro?->format('Y-m-d') ?? '' }}"
                            data-fin-reg="{{ $periodo->fecha_fin_registro?->format('Y-m-d') ?? '' }}">
                            <td class="px-6 py-4 font-mono text-xs font-bold whitespace-nowrap" style="color: #0F4229;">
                                {{ $periodo->nombre }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $periodo->label }}</td>
                            <td class="px-6 py-4 text-gray-600 text-xs">
                                {{ $periodo->fecha_inicio_clases ? $periodo->fecha_inicio_clases->format('d/m/Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs">
                                {{ $periodo->fecha_fin_clases ? $periodo->fecha_fin_clases->format('d/m/Y') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="abrirEditarCuatrimestre({{ $periodo->id }}, '{{ $periodo->nombre }}', '{{ addslashes($periodo->label) }}', '{{ $periodo->fecha_inicio_clases?->format('Y-m-d') ?? '' }}', '{{ $periodo->fecha_fin_clases?->format('Y-m-d') ?? '' }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                            style="background-color: #D4AF37;"
                                            onmouseover="this.style.backgroundColor='#b8962e'"
                                            onmouseout="this.style.backgroundColor='#D4AF37'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </button>
                                    @if($periodo->grupos_count === 0)
                                    <form method="POST" action="{{ route('admin.periodos.destroy', $periodo->id) }}"
                                          onsubmit="return confirm('¿Eliminar \"{{ addslashes($periodo->label) }}\"? Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border border-red-200 text-red-500 hover:bg-red-50 transition-colors duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                No hay cuatrimestres registrados. Crea el primero.
                            </td>
                        </tr>
                        @endforelse
                        <tr x-ref="sinResultadosCuatrimestres" style="display: none;">
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                Ningún cuatrimestre coincide con la búsqueda.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>{{-- /tab cuatrimestres --}}

    {{-- ══ TAB: INSCRIPCIONES ══ --}}
    <div x-show="tab === 'inscripciones'" x-cloak
         x-data="{
             busqueda: '',
             filtroModo: '',
             filtroEstado: '',
             filtrar() {
                 this.$nextTick(() => {
                     const filas = this.$refs.tbodyInscripciones.querySelectorAll('tr[data-periodo-id]');
                     let visibles = 0;
                     filas.forEach(f => {
                         const texto = this.busqueda.toLowerCase();
                         const pasaBusq   = !texto
                             || f.dataset.nombre.toLowerCase().includes(texto)
                             || f.dataset.label.toLowerCase().includes(texto);
                         const pasaModo   = !this.filtroModo   || f.dataset.modo   === this.filtroModo;
                         const pasaEstado = !this.filtroEstado || f.dataset.estado === this.filtroEstado;
                         const mostrar = pasaBusq && pasaModo && pasaEstado;
                         f.style.display = mostrar ? '' : 'none';
                         if (mostrar) visibles++;
                     });
                     this.$refs.sinResultadosInscripciones.style.display = visibles === 0 ? '' : 'none';
                     this.$refs.contadorInscripciones.textContent = visibles + ' registro' + (visibles !== 1 ? 's' : '');
                 });
             }
         }">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $periodos->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Activo</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">{{ $periodos->where('estado','activo')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 col-span-2 sm:col-span-1" style="border-color: #9ca3af;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Cerrados</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $periodos->where('estado','cerrado')->count() }}</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" x-model="busqueda" @input="filtrar()"
                       placeholder="Buscar por cuatrimestre…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div class="relative sm:w-44">
                <select x-model="filtroModo" @change="filtrar()"
                        class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los modos</option>
                    <option value="auto">Auto</option>
                    <option value="manual">Manual</option>
                </select>
            </div>
            <div class="relative sm:w-44">
                <select x-model="filtroEstado" @change="filtrar()"
                        class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                    <option value="cerrado">Cerrado</option>
                    <option value="sin_configurar">Sin configurar</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Periodos de inscripción</h2>
                <span class="text-xs text-gray-400" x-ref="contadorInscripciones">{{ $periodos->count() }} registro{{ $periodos->count() !== 1 ? 's' : '' }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Cuatrimestre</th>
                            <th class="px-6 py-3">Apertura</th>
                            <th class="px-6 py-3">Cierre</th>
                            <th class="px-6 py-3 text-center">Modo</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tbodyInscripciones">
                        @forelse($periodos as $periodo)
                        <tr class="hover:bg-gray-50 transition-colors duration-100"
                            data-periodo-id="{{ $periodo->id }}"
                            data-nombre="{{ $periodo->nombre }}"
                            data-label="{{ addslashes($periodo->label) }}"
                            data-modo="{{ !$periodo->fecha_inicio_registro ? '' : ($periodo->auto ? 'auto' : 'manual') }}"
                            data-estado="{{ !$periodo->fecha_inicio_registro ? 'sin_configurar' : $periodo->estado }}"
                            data-inicio-clases="{{ $periodo->fecha_inicio_clases?->format('Y-m-d') ?? '' }}"
                            data-fin-clases="{{ $periodo->fecha_fin_clases?->format('Y-m-d') ?? '' }}">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-800">{{ $periodo->label }}</p>
                                <p class="text-xs font-mono mt-0.5" style="color: #0F4229;">{{ $periodo->nombre }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs">
                                {{ $periodo->fecha_inicio_registro ? $periodo->fecha_inicio_registro->format('d/m/Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs">
                                {{ $periodo->fecha_fin_registro ? $periodo->fecha_fin_registro->format('d/m/Y') : '—' }}
                            </td>
                            {{-- Modo (solo si ya tiene fechas configuradas) --}}
                            <td class="px-6 py-4 text-center">
                                @if($periodo->fecha_inicio_registro)
                                <form method="POST" action="{{ route('admin.periodos.toggleAuto', $periodo->id) }}">
                                    @csrf @method('PATCH')
                                    @if($periodo->auto)
                                    <button type="submit" title="Modo automático — clic para cambiar a manual"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-white transition-colors duration-150"
                                            style="background-color: #0F4229;"
                                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                                            onmouseout="this.style.backgroundColor='#0F4229'">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Auto
                                    </button>
                                    @else
                                    <button type="submit" title="Modo manual — clic para cambiar a automático"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200 hover:bg-gray-300 transition-colors duration-150">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Manual
                                    </button>
                                    @endif
                                </form>
                                @else
                                <span class="text-xs text-gray-400 italic">Sin configurar</span>
                                @endif
                            </td>
                            {{-- Estado --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(!$periodo->fecha_inicio_registro)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-500 bg-gray-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 inline-block"></span>Sin configurar
                                </span>
                                @elseif($periodo->estado === 'activo')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #0F4229;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>Activo
                                </span>
                                @elseif($periodo->estado === 'inactivo')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #EFAD5A;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>Inactivo
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>Cerrado
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if(!$periodo->fecha_inicio_registro)
                                    {{-- Sin fechas: mostrar botón Configurar --}}
                                    <button onclick="abrirConfigurarInscripcion({{ $periodo->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                            style="background-color: #0F4229;"
                                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                                            onmouseout="this.style.backgroundColor='#0F4229'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Configurar
                                    </button>
                                    @else
                                    {{-- Ya tiene fechas: Editar + Abrir/Cerrar --}}
                                    <button onclick="abrirEditarInscripcion({{ $periodo->id }}, '{{ $periodo->fecha_inicio_registro->format('Y-m-d') }}', '{{ $periodo->fecha_fin_registro->format('Y-m-d') }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                            style="background-color: #D4AF37;"
                                            onmouseover="this.style.backgroundColor='#b8962e'"
                                            onmouseout="this.style.backgroundColor='#D4AF37'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar fechas
                                    </button>
                                    @if($periodo->fecha_inicio_registro && $periodo->estado !== 'activo')
                                    <form method="POST" action="{{ route('admin.periodos.activar', $periodo->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                                style="background-color: #EFAD5A;"
                                                onmouseover="this.style.backgroundColor='#e09a3a'"
                                                onmouseout="this.style.backgroundColor='#EFAD5A'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Abrir
                                        </button>
                                    </form>
                                    @endif
                                    @if($periodo->fecha_inicio_registro && $periodo->estado === 'activo')
                                    <form method="POST" action="{{ route('admin.periodos.cerrar', $periodo->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-gray-400 hover:bg-gray-500 transition-colors duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Cerrar
                                        </button>
                                    </form>
                                    @endif
                                    @endif {{-- /if fecha_inicio_registro --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                                No hay cuatrimestres. Crea uno en el tab "Cuatrimestres".
                            </td>
                        </tr>
                        @endforelse
                        <tr x-ref="sinResultadosInscripciones" style="display: none;">
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                                Ningún periodo coincide con la búsqueda o los filtros.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>{{-- /tab inscripciones --}}

    {{-- ══ TAB: CARRERAS ══ --}}
    <div x-show="tab === 'carreras'" x-cloak>
        @if($periodos->isEmpty())
        <div class="bg-white rounded-2xl shadow-md px-6 py-16 text-center">
            <p class="text-sm text-gray-400">No hay cuatrimestres creados todavía.</p>
        </div>
        @else
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($periodos as $p)
            <button @click="periodoId = {{ $p->id }}"
                    :class="periodoId === {{ $p->id }} ? 'text-white shadow-sm' : 'text-gray-600 bg-white hover:bg-gray-50'"
                    :style="periodoId === {{ $p->id }} ? 'background-color:#0F4229' : ''"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold border border-gray-200 transition-all duration-150">
                {{ $p->nombre }}
                @if($p->estado === 'activo')
                <span class="ml-1 inline-block w-1.5 h-1.5 rounded-full bg-green-400 align-middle"></span>
                @endif
            </button>
            @endforeach
        </div>

        @foreach($periodos as $p)
        @php
            $agrupados        = $p->programas->groupBy('nivel');
            $gruposDelPeriodo = $gruposPorPeriodo[$p->id] ?? collect();
            $gruposPorPrograma= $gruposDelPeriodo->groupBy('programa_id');
            $disponibles      = $programasActivos->whereNotIn('id', $p->programas->pluck('id'));
            $totalCarreras    = $p->programas->count();
            $totalGrupos      = $gruposDelPeriodo->count();
            $totalAlumnos     = $gruposDelPeriodo->sum('alumnos_count');
        @endphp
        <div x-show="periodoId === {{ $p->id }}" x-cloak
             x-data="{
                 busqueda: '',
                 filtroNivel: '',
                 filtroEstado: '',
                 filtrar() {
                     this.$nextTick(() => {
                         const filas = this.$refs.tbodyCarreras.querySelectorAll('tr[data-nombre]');
                         let visibles = 0;
                         filas.forEach(f => {
                             const texto = this.busqueda.toLowerCase();
                             const pasaBusq   = !texto
                                 || f.dataset.nombre.includes(texto)
                                 || f.dataset.clave.includes(texto);
                             const pasaNivel  = !this.filtroNivel  || f.dataset.nivel  === this.filtroNivel;
                             const pasaEstado = !this.filtroEstado || f.dataset.activo === this.filtroEstado;
                             const mostrar = pasaBusq && pasaNivel && pasaEstado;
                             f.style.display = mostrar ? '' : 'none';
                             if (mostrar) visibles++;
                         });
                         this.$refs.sinResultadosCarreras.style.display = visibles === 0 ? '' : 'none';
                         this.$refs.contadorCarreras.textContent = visibles + ' carrera' + (visibles !== 1 ? 's' : '');
                     });
                 }
             }">
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Carreras</p>
                    <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $totalCarreras }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Grupos</p>
                    <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">{{ $totalGrupos }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #9ca3af;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Alumnos</p>
                    <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $totalAlumnos }}</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" x-model="busqueda" @input="filtrar()"
                           placeholder="Buscar por nombre o clave…"
                           class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>
                <div class="relative sm:w-48">
                    <select x-model="filtroNivel" @change="filtrar()"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">Todos los niveles</option>
                        <option value="licenciatura">Licenciatura</option>
                        <option value="maestria">Maestría</option>
                        <option value="doctorado">Doctorado</option>
                    </select>
                </div>
                <div class="relative sm:w-44">
                    <select x-model="filtroEstado" @change="filtrar()"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">Todos los estados</option>
                        <option value="1">Activa</option>
                        <option value="0">Pausada</option>
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">{{ $p->label }}</h2>
                    <span class="text-xs text-gray-400" x-ref="contadorCarreras">{{ $totalCarreras }} carrera{{ $totalCarreras !== 1 ? 's' : '' }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Carrera</th>
                                <th class="px-6 py-3">Nivel</th>
                                <th class="px-6 py-3 text-center">Grupos</th>
                                <th class="px-6 py-3 text-center">Estado</th>
                                <th class="px-6 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" x-ref="tbodyCarreras">
                            @forelse($p->programas as $prog)
                            @php
                                $gs  = $gruposPorPrograma[$prog->id] ?? collect();
                                $cfg = $nivelCfg[$prog->nivel] ?? ['label' => ucfirst($prog->nivel), 'color' => '#9ca3af'];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-100"
                                data-nombre="{{ strtolower($prog->nombre) }}"
                                data-clave="{{ strtolower($prog->clave) }}"
                                data-nivel="{{ $prog->nivel }}"
                                data-activo="{{ $prog->pivot->activo ? '1' : '0' }}">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-800">{{ $prog->nombre }}</p>
                                    <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $prog->clave }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold text-white" style="background-color: {{ $cfg['color'] }};">{{ $cfg['label'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($gs->isNotEmpty())
                                        <span class="font-semibold text-gray-700">{{ $gs->count() }}</span>
                                        <span class="text-gray-400 text-xs"> ({{ $gs->sum('alumnos_count') }}/{{ $gs->sum('capacidad') }})</span>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Sin grupos</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('admin.periodos.programas.toggle', [$p->id, $prog->id]) }}">
                                        @csrf @method('PATCH')
                                        @if($prog->pivot->activo)
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #0F4229;" onmouseover="this.style.backgroundColor='#0a2e1c'" onmouseout="this.style.backgroundColor='#0F4229'">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>Activa
                                        </button>
                                        @else
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #EFAD5A;" onmouseover="this.style.backgroundColor='#d4922e'" onmouseout="this.style.backgroundColor='#EFAD5A'">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>Pausada
                                        </button>
                                        @endif
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('admin.periodos.programas.destroy', [$p->id, $prog->id]) }}" onsubmit="return confirm('¿Quitar {{ addslashes($prog->nombre) }} de este cuatrimestre?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border border-red-200 text-red-500 hover:bg-red-50 transition-colors duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Quitar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">No hay carreras configuradas. Usa "Agregar carrera".</td></tr>
                            @endforelse
                            <tr x-ref="sinResultadosCarreras" style="display: none;">
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">Ninguna carrera coincide con la búsqueda o los filtros.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Modal agregar carrera (fuera del x-show para que no quede oculto por display:none) --}}
        <div id="modal-agregar-{{ $p->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">Agregar carrera — {{ $p->label }}</h3>
                    <button onclick="document.getElementById('modal-agregar-{{ $p->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.periodos.programas.store', $p->id) }}" class="px-6 py-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carrera <span class="text-red-500">*</span></label>
                        @php $yaEnPeriodo = $p->programas->pluck('id'); @endphp
                        <select name="programa_id" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none" onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'" onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'" required>
                            <option value="">Seleccionar</option>
                            @php $nivelActual = null; @endphp
                            @foreach($programasActivos as $dProg)
                                @if($dProg->nivel !== $nivelActual)
                                    @if($nivelActual !== null)</optgroup>@endif
                                    @php $nivelActual = $dProg->nivel; @endphp
                                    <optgroup label="{{ $nivelCfg[$dProg->nivel]['label'] ?? ucfirst($dProg->nivel) }}">
                                @endif
                                @if($yaEnPeriodo->contains($dProg->id))
                                    <option value="" disabled>{{ $dProg->nombre }} (ya en este cuatrimestre)</option>
                                @else
                                    <option value="{{ $dProg->id }}">{{ $dProg->nombre }}</option>
                                @endif
                            @endforeach
                            @if($nivelActual !== null)</optgroup>@endif
                        </select>
                        @if($disponibles->isEmpty())
                        <p class="text-xs text-gray-400 mt-1">Todos los programas activos ya están en este cuatrimestre. Crea más en <a href="{{ route('admin.programas.index') }}" class="underline" style="color:#0F4229;">Programas académicos</a>.</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grupos a crear <span class="text-red-500">*</span></label>
                            <input type="number" name="num_grupos" placeholder="Ej. 2" min="1" max="10" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none" onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'" onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'" required>
                            <p class="text-xs text-gray-400 mt-1">Clave: XXX-{{ $p->nombre }}-A, B, C...</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad por grupo <span class="text-red-500">*</span></label>
                            <input type="number" name="capacidad" placeholder="Ej. 20" min="1" max="500" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none" onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'" onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'" required>
                            <p class="text-xs text-gray-400 mt-1">Máximo de alumnos por grupo.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="document.getElementById('modal-agregar-{{ $p->id }}').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors" style="background-color: #0F4229;" onmouseover="this.style.backgroundColor='#0a2e1c'" onmouseout="this.style.backgroundColor='#0F4229'">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
        @endif
    </div>{{-- /tab carreras --}}

</div>
</section>

{{-- ══ MODAL: Nuevo cuatrimestre ══ --}}
<div id="modal-crear" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Nuevo cuatrimestre</h3>
            <button onclick="document.getElementById('modal-crear').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.periodos.store') }}" class="px-6 py-5 space-y-5">
            @csrf
            {{-- Sección 1: Cuatrimestre --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: #0F4229;">Cuatrimestre</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Clave <span class="text-red-500">*</span></label>
                        <input type="text" id="crear-nombre" name="nombre" placeholder="Ej. 2026-2" maxlength="6" autocomplete="off"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none font-mono"
                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'" required>
                        <p class="text-xs text-gray-400 mt-1">Formato año-número (ej: 2026-2)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                        <input type="text" name="label" placeholder="Ej. Segundo Cuatrimestre 2026" maxlength="100"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'" required>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Inicio de clases</label>
                            <input type="date" name="fecha_inicio_clases"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                                   onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                                   onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fin de clases</label>
                            <input type="date" name="fecha_fin_clases"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                                   onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                                   onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-400">Las fechas de inscripción se configuran después desde el tab <strong>Inscripciones</strong>.</p>
            <div class="flex justify-end gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modal-crear').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors" style="background-color: #0F4229;" onmouseover="this.style.backgroundColor='#0a2e1c'" onmouseout="this.style.backgroundColor='#0F4229'">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL: Editar cuatrimestre (solo fechas clases + nombre) ══ --}}
<div id="modal-editar-cuatrimestre" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #D4AF37;"></div>
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Editar cuatrimestre</h3>
            <button onclick="document.getElementById('modal-editar-cuatrimestre').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-editar-cuatrimestre" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Clave <span class="text-red-500">*</span></label>
                <input type="text" id="ec-nombre" name="nombre" maxlength="6"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none font-mono"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                <input type="text" id="ec-label" name="label" maxlength="100"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Inicio de clases</label>
                    <input type="date" id="ec-inicio-clases" name="fecha_inicio_clases"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fin de clases</label>
                    <input type="date" id="ec-fin-clases" name="fecha_fin_clases"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>
            </div>
            {{-- Campos ocultos para mantener las fechas de inscripción --}}
            <input type="hidden" id="ec-inicio-reg" name="fecha_inicio_registro">
            <input type="hidden" id="ec-fin-reg" name="fecha_fin_registro">
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-editar-cuatrimestre').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors" style="background-color: #D4AF37;" onmouseover="this.style.backgroundColor='#b8962e'" onmouseout="this.style.backgroundColor='#D4AF37'">Actualizar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL: Editar periodo de inscripción ══ --}}
<div id="modal-editar-inscripcion" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #D4AF37;"></div>
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Editar periodo de inscripción</h3>
            <button onclick="document.getElementById('modal-editar-inscripcion').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-editar-inscripcion" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <p class="text-xs text-gray-500">Define las fechas en las que los aspirantes pueden registrarse.</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apertura <span class="text-red-500">*</span></label>
                    <input type="date" id="ei-inicio-reg" name="fecha_inicio_registro"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cierre <span class="text-red-500">*</span></label>
                    <input type="date" id="ei-fin-reg" name="fecha_fin_registro"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'" required>
                </div>
            </div>
            {{-- Campos ocultos para mantener el resto de los datos --}}
            <input type="hidden" id="ei-nombre" name="nombre">
            <input type="hidden" id="ei-label" name="label">
            <input type="hidden" id="ei-inicio-clases" name="fecha_inicio_clases">
            <input type="hidden" id="ei-fin-clases" name="fecha_fin_clases">
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-editar-inscripcion').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors" style="background-color: #D4AF37;" onmouseover="this.style.backgroundColor='#b8962e'" onmouseout="this.style.backgroundColor='#D4AF37'">Guardar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Editar cuatrimestre (fechas de clases + nombre)
function abrirEditarCuatrimestre(id, nombre, label, inicioClases, finClases) {
    // También recuperamos las fechas de inscripción del DOM para pasarlas como hidden
    // Buscamos en los datos de la tabla de inscripciones
    const rows = document.querySelectorAll('[data-periodo-id="' + id + '"]');
    let inicioReg = '', finReg = '';
    if (rows.length > 0) {
        inicioReg = rows[0].dataset.inicioReg || '';
        finReg    = rows[0].dataset.finReg    || '';
    }
    document.getElementById('form-editar-cuatrimestre').action = '/admin/periodos/' + id;
    document.getElementById('ec-nombre').value       = nombre;
    document.getElementById('ec-label').value        = label;
    document.getElementById('ec-inicio-clases').value = inicioClases || '';
    document.getElementById('ec-fin-clases').value    = finClases || '';
    document.getElementById('ec-inicio-reg').value    = inicioReg;
    document.getElementById('ec-fin-reg').value       = finReg;
    document.getElementById('modal-editar-cuatrimestre').classList.remove('hidden');
}

// Configurar inscripción (primera vez, sin fechas)
function abrirConfigurarInscripcion(id) {
    document.getElementById('form-editar-inscripcion').action = '/admin/periodos/' + id + '/inscripcion';
    document.getElementById('ei-inicio-reg').value    = '';
    document.getElementById('ei-fin-reg').value       = '';
    document.getElementById('ei-nombre').value        = '';
    document.getElementById('ei-label').value         = '';
    document.getElementById('ei-inicio-clases').value = '';
    document.getElementById('ei-fin-clases').value    = '';
    document.getElementById('modal-editar-inscripcion').classList.remove('hidden');
}

// Editar inscripción (ya tiene fechas)
function abrirEditarInscripcion(id, inicioReg, finReg) {
    const el = document.querySelector('[data-periodo-id="' + id + '"]');
    document.getElementById('form-editar-inscripcion').action = '/admin/periodos/' + id + '/inscripcion';
    document.getElementById('ei-inicio-reg').value    = inicioReg;
    document.getElementById('ei-fin-reg').value       = finReg;
    document.getElementById('ei-nombre').value        = el ? el.dataset.nombre       : '';
    document.getElementById('ei-label').value         = el ? el.dataset.label        : '';
    document.getElementById('ei-inicio-clases').value = el ? el.dataset.inicioClases : '';
    document.getElementById('ei-fin-clases').value    = el ? el.dataset.finClases    : '';
    document.getElementById('modal-editar-inscripcion').classList.remove('hidden');
}

document.getElementById('crear-nombre').addEventListener('input', function () {
    let val = this.value.replace(/[^\d]/g, '').slice(0, 5);
    if (val.length > 4) val = val.slice(0, 4) + '-' + val.slice(4);
    this.value = val;
});

@if($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('modal-crear').classList.remove('hidden');
});
@endif
</script>
@endpush
