@extends('layouts.app')

@section('title', 'Carga Académica | UICM')

@section('content')

<div x-data="{ cargando: false }">

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Coordinación Académica
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Generar carga académica</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- ══════════════════════════════════════════
             MENSAJE DE ÉXITO
        ══════════════════════════════════════════ --}}
        @if (session('success'))
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-10 flex flex-col items-center text-center">

                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-5"
                     style="background-color: #f0f9f4;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h2 class="text-xl font-extrabold text-gray-900 mb-1">
                    ¡Carga académica generada correctamente!
                </h2>
                <p class="text-sm text-gray-500 mb-6">
                    {{ session('success') }}
                </p>

                @if ($grupo)
                <div class="grid grid-cols-3 gap-4 w-full max-w-sm mb-8">
                    <div class="bg-uicm-gray rounded-xl py-3 px-2 text-center">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Alumnos</p>
                        <p class="text-xl font-extrabold" style="color: #0F4229;">{{ $grupo->alumnos->count() }}</p>
                    </div>
                    <div class="bg-uicm-gray rounded-xl py-3 px-2 text-center">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Materias</p>
                        <p class="text-xl font-extrabold" style="color: #0F4229;">{{ $carga->count() }}</p>
                    </div>
                    <div class="bg-uicm-gray rounded-xl py-3 px-2 text-center">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Profesores</p>
                        <p class="text-xl font-extrabold" style="color: #0F4229;">
                            {{ $carga->whereNotNull('profesor_id')->pluck('profesor_id')->unique()->count() }}
                        </p>
                    </div>
                </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('admin.carga-academica.index') }}"
                       class="px-6 py-2.5 rounded-xl text-sm font-bold border-2 transition-colors duration-150 text-center"
                       style="border-color: #0F4229; color: #0F4229;"
                       onmouseover="this.style.backgroundColor='#0F4229'; this.style.color='white'"
                       onmouseout="this.style.backgroundColor='transparent'; this.style.color='#0F4229'">
                        Generar otra carga
                    </a>
                </div>

            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════
             FORMULARIO DE FILTROS
        ══════════════════════════════════════════ --}}
        <div class="space-y-6">

            {{-- Card de filtros --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #D4AF37;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414
                                 a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293
                                 A1 1 0 013 6.586V4z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Filtros de búsqueda</h2>
                </div>

                <div class="px-6 py-5"
                     x-data="{
                         periodo_id: '{{ request('periodo_id', '') }}',
                         programa_id: '{{ request('programa_id', '') }}',
                         cuatrimestre: '{{ request('cuatrimestre', '') }}',
                         grupos: @js($grupos->map(fn($g) => ['id' => $g->id, 'clave' => $g->clave, 'periodo_id' => $g->periodo_id, 'programa_id' => $g->programa_id, 'cuatrimestre' => $g->cuatrimestre])),
                         get gruposFiltrados() {
                             return this.grupos.filter(g =>
                                 (!this.periodo_id   || g.periodo_id    == this.periodo_id) &&
                                 (!this.programa_id  || g.programa_id   == this.programa_id) &&
                                 (!this.cuatrimestre || g.cuatrimestre  == this.cuatrimestre)
                             );
                         }
                     }">

                    <form method="GET" action="{{ route('admin.carga-academica.index') }}" @submit="cargando = true">

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

                            {{-- Periodo --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                    Periodo
                                </label>
                                <select name="periodo_id" x-model="periodo_id"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700
                                               outline-none transition-all duration-150 bg-white"
                                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                                        onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                                    <option value="">Todos los periodos</option>
                                    @foreach ($periodos as $p)
                                        <option value="{{ $p->id }}">{{ $p->label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Cuatrimestre --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                    Cuatrimestre
                                </label>
                                <select name="cuatrimestre" x-model="cuatrimestre"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700
                                               outline-none transition-all duration-150 bg-white"
                                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                                        onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                                    <option value="">Todos</option>
                                    @foreach (range(1, 9) as $n)
                                        <option value="{{ $n }}">{{ $n }}°</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Programa --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                    Programa
                                </label>
                                <select name="programa_id" x-model="programa_id"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700
                                               outline-none transition-all duration-150 bg-white"
                                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                                        onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                                    <option value="">Todos</option>
                                    @foreach ($programas as $prog)
                                        <option value="{{ $prog->id }}">{{ $prog->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Grupo (filtrado por los demás selects vía Alpine) --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                    Grupo
                                </label>
                                <select name="grupo_id"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700
                                               outline-none transition-all duration-150 bg-white"
                                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                                        onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                                    <option value="">Seleccionar grupo</option>
                                    <template x-for="g in gruposFiltrados" :key="g.id">
                                        <option :value="g.id"
                                                :selected="g.id == {{ request('grupo_id', 0) }}"
                                                x-text="g.clave">
                                        </option>
                                    </template>
                                </select>
                            </div>

                        </div>

                        {{-- Botón Cargar --}}
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="cargando"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white
                                       shadow-sm transition-all duration-150"
                                style="background-color: #0F4229;"
                                onmouseover="if(!this.disabled) this.style.backgroundColor='#0a2e1c'"
                                onmouseout="if(!this.disabled) this.style.backgroundColor='#0F4229'">

                                <svg x-show="cargando" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"
                                     style="display:none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"></path>
                                </svg>

                                <svg x-show="!cargando" class="w-4 h-4" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9
                                             m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>

                                <span x-text="cargando ? 'Cargando...' : 'Cargar información'"></span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            {{-- ══════════════════════════════════════════
                 INFO DEL GRUPO (visible si se seleccionó uno)
            ══════════════════════════════════════════ --}}
            @if ($grupo)

                {{-- Tarjeta resumen del grupo --}}
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                    <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: #0F4229;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                                         M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                                         m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <h2 class="text-sm font-semibold text-gray-700">
                                Grupo {{ $grupo->clave }} — {{ $grupo->programa->nombre }}
                            </h2>
                        </div>
                        @if ($carga->isNotEmpty())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-white"
                                  style="background-color: #0F4229;">
                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                Carga configurada
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-white"
                                  style="background-color: #EFAD5A;">
                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                Sin carga asignada
                            </span>
                        @endif
                    </div>

                    <div class="px-6 py-5">

                        {{-- Stats --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

                            <div class="bg-uicm-gray rounded-xl px-5 py-4 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background-color: #e6f2ec;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                         style="color: #0F4229;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total alumnos</p>
                                    <p class="text-2xl font-extrabold" style="color: #0F4229;">
                                        {{ $grupo->alumnos->count() }}
                                    </p>
                                </div>
                            </div>

                            <div class="bg-uicm-gray rounded-xl px-5 py-4 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background-color: #fdf8ec;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                         style="color: #D4AF37;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                                                 C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                                                 C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                                                 C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total materias</p>
                                    <p class="text-2xl font-extrabold" style="color: #D4AF37;">
                                        {{ $carga->count() }}
                                    </p>
                                </div>
                            </div>

                            <div class="bg-uicm-gray rounded-xl px-5 py-4 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background-color: #fef4e8;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                         style="color: #EFAD5A;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total profesores</p>
                                    <p class="text-2xl font-extrabold" style="color: #EFAD5A;">
                                        {{ $carga->whereNotNull('profesor_id')->pluck('profesor_id')->unique()->count() }}
                                    </p>
                                </div>
                            </div>

                        </div>

                        {{-- Info adicional --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="bg-uicm-gray rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Periodo</p>
                                <p class="text-sm font-bold text-gray-800">{{ $grupo->periodo->nombre ?? '—' }}</p>
                            </div>
                            <div class="bg-uicm-gray rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Cuatrimestre</p>
                                <p class="text-sm font-bold" style="color: #0F4229;">{{ $grupo->cuatrimestre }}°</p>
                            </div>
                            <div class="bg-uicm-gray rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Grupo</p>
                                <p class="text-sm font-bold" style="color: #0F4229;">{{ $grupo->clave }}</p>
                            </div>
                            <div class="bg-uicm-gray rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Capacidad</p>
                                <p class="text-sm font-bold" style="color: #0F4229;">{{ $grupo->capacidad ?? '—' }}</p>
                            </div>
                        </div>

                    </div>
                </div>

                @if ($carga->isNotEmpty())
                {{-- Tabla de materias del grupo --}}
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                    <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: #D4AF37;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                         M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h2 class="text-sm font-semibold text-gray-700">Materias asignadas al grupo</h2>
                        </div>
                        <span class="text-xs text-gray-400">{{ $carga->count() }} materias</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3">Clave</th>
                                    <th class="px-6 py-3">Materia</th>
                                    <th class="px-6 py-3">Profesor asignado</th>
                                    <th class="px-6 py-3">Horario</th>
                                    <th class="px-6 py-3">Aula</th>
                                    <th class="px-6 py-3 text-center">Créditos</th>
                                    <th class="px-6 py-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($carga as $entrada)
                                <tr class="hover:bg-gray-50 transition-colors duration-100">
                                    <td class="px-6 py-3 font-mono text-xs font-semibold text-gray-500">
                                        {{ $entrada->materia->clave ?? '—' }}
                                    </td>
                                    <td class="px-6 py-3 font-semibold text-gray-800">
                                        {{ $entrada->materia->nombre ?? '—' }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @if($entrada->profesor)
                                            <span class="text-gray-700">{{ $entrada->profesor->nombre }}</span>
                                        @else
                                            <span class="text-orange-500 font-medium text-xs">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $entrada->horario ?? '—' }}</td>
                                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $entrada->aula ?? '—' }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white"
                                              style="background-color: #D4AF37;">
                                            {{ $entrada->materia->creditos ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <button onclick="abrirAsignacion({{ $entrada->id }}, '{{ addslashes($entrada->materia->nombre ?? '') }}', {{ $entrada->profesor_id ?? 'null' }}, '{{ addslashes($entrada->horario ?? '') }}', '{{ addslashes($entrada->aula ?? '') }}')"
                                                class="text-xs font-medium px-3 py-1.5 rounded-lg border transition-colors duration-150"
                                                style="border-color: #0F4229; color: #0F4229;"
                                                onmouseover="this.style.backgroundColor='#0F4229'; this.style.color='white'"
                                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#0F4229'">
                                            Asignar
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- ══════════════════════════════════════════
                     ACCIÓN PRINCIPAL
                ══════════════════════════════════════════ --}}
                @if ($carga->isEmpty())
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                    <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                    <div class="px-6 py-8 flex flex-col items-center text-center">

                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4"
                             style="background-color: #f0f9f4;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: #0F4229;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806
                                         3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806
                                         3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946
                                         3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946
                                         3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806
                                         3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806
                                         3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946
                                         3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946
                                         3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>

                        <h3 class="text-base font-extrabold text-gray-900 mb-1">Todo listo</h3>
                        <p class="text-sm text-gray-500 mb-6 max-w-xs">
                            Se generará la carga académica para el grupo
                            <strong>{{ $grupo->clave }}</strong> ({{ $grupo->cuatrimestre }}° cuatrimestre)
                            del periodo <strong>{{ $grupo->periodo->label ?? '—' }}</strong>, asignando las materias del programa
                            <strong>{{ $grupo->programa->nombre }}</strong>.
                        </p>

                        <form method="POST"
                              action="{{ route('admin.carga-academica.generar', $grupo->id) }}">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center gap-3 px-10 py-3.5 rounded-2xl text-base font-extrabold
                                       text-white shadow-lg transition-all duration-150"
                                style="background-color: #0F4229;"
                                onmouseover="this.style.backgroundColor='#0a2e1c'; this.style.transform='scale(1.02)'"
                                onmouseout="this.style.backgroundColor='#0F4229'; this.style.transform='scale(1)'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Generar carga académica
                            </button>
                        </form>

                    </div>
                </div>
                @endif

            @endif

        </div>{{-- /space-y-6 --}}


    </div>
</section>

</div>{{-- /x-data --}}

{{-- Modal asignación de profesor/horario/aula --}}
<div id="modal-asignacion" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-800">Asignar profesor y horario</h3>
                <p id="modal-materia-nombre" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button onclick="document.getElementById('modal-asignacion').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-asignacion" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Profesor</label>
                <select id="asig-profesor" name="profesor_id"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200">
                    <option value="">— Sin asignar —</option>
                    @foreach($profesores as $prof)
                        <option value="{{ $prof->id }}">{{ $prof->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario</label>
                    <input type="text" id="asig-horario" name="horario"
                           placeholder="ej: Lun-Mié 08:00-10:00"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aula</label>
                    <input type="text" id="asig-aula" name="aula"
                           placeholder="ej: A-101"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-asignacion').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2f1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirAsignacion(cargaId, materiaNombre, profesorId, horario, aula) {
    document.getElementById('form-asignacion').action = '/admin/carga-academica/' + cargaId + '/actualizar';
    document.getElementById('modal-materia-nombre').textContent = materiaNombre;
    document.getElementById('asig-profesor').value = profesorId ?? '';
    document.getElementById('asig-horario').value = horario;
    document.getElementById('asig-aula').value = aula;
    document.getElementById('modal-asignacion').classList.remove('hidden');
}
</script>
@endpush
