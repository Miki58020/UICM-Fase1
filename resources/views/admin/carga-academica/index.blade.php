@extends('layouts.app')

@section('title', 'Carga Académica | UICM')

@section('content')

@php
// Opciones de filtros
$ciclos = ['2025-1', '2025-2', '2026-1'];

$programas = [
    'Ingeniería en Sistemas Computacionales',
    'Derecho',
    'Administración de Empresas',
    'Contaduría Pública',
    'Psicología',
];

$grupos = [
    'IS-101', 'IS-102',
    'DER-101',
    'ADM-101', 'ADM-102',
    'CON-101',
    'PSI-101',
];

// Datos simulados del grupo
$infoGrupo = [
    'grupo'      => 'IS-101',
    'programa'   => 'Ingeniería en Sistemas Computacionales',
    'ciclo'      => '2026-1',
    'cuatrimestre'=> '1°',
    'alumnos'    => 28,
    'materias'   => 6,
    'profesores' => 6,
];

$materiasGrupo = [
    ['clave' => 'IS-101', 'nombre' => 'Programación I',             'creditos' => 8, 'profesor' => 'Dr. Alejandro Fuentes Mora'],
    ['clave' => 'MAT-101','nombre' => 'Cálculo Diferencial',         'creditos' => 8, 'profesor' => 'Dr. Luis Medina Torres'],
    ['clave' => 'FIS-101','nombre' => 'Física I',                    'creditos' => 6, 'profesor' => 'Ing. Ricardo Vázquez Pérez'],
    ['clave' => 'HUM-101','nombre' => 'Habilidades del Pensamiento', 'creditos' => 4, 'profesor' => 'Mtra. Carmen Ortiz Salinas'],
    ['clave' => 'ADM-101','nombre' => 'Introducción a la Administración','creditos'=> 4,'profesor'=> 'Lic. Patricia Herrera Ramos'],
    ['clave' => 'ING-101','nombre' => 'Inglés I',                    'creditos' => 4, 'profesor' => 'Mtra. Sofía Ríos Castillo'],
];
@endphp

<div x-data="{
    cargado:  false,
    generado: false,
    cargando: false,
    cargar() {
        this.cargando = true;
        this.generado = false;
        setTimeout(() => { this.cargando = false; this.cargado = true; }, 800);
    },
    generar() {
        this.generado = true;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}">

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
        <div
            x-show="generado"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white rounded-2xl shadow-md overflow-hidden mb-6"
            style="display: none;">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-10 flex flex-col items-center text-center">

                {{-- Ícono check --}}
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
                    El grupo <span class="font-bold text-gray-700">IS-101</span> del ciclo
                    <span class="font-bold text-gray-700">2026-1</span> ha sido configurado exitosamente.
                </p>

                {{-- Resumen rápido --}}
                <div class="grid grid-cols-3 gap-4 w-full max-w-sm mb-8">
                    <div class="bg-uicm-gray rounded-xl py-3 px-2 text-center">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Alumnos</p>
                        <p class="text-xl font-extrabold" style="color: #0F4229;">28</p>
                    </div>
                    <div class="bg-uicm-gray rounded-xl py-3 px-2 text-center">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Materias</p>
                        <p class="text-xl font-extrabold" style="color: #0F4229;">6</p>
                    </div>
                    <div class="bg-uicm-gray rounded-xl py-3 px-2 text-center">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Profesores</p>
                        <p class="text-xl font-extrabold" style="color: #0F4229;">6</p>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        @click="generado = false; cargado = false"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold border-2 transition-colors duration-150"
                        style="border-color: #0F4229; color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0F4229'; this.style.color='white'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#0F4229'">
                        Generar otra carga
                    </button>
                    <a href="{{ route('dashboard') }}"
                       class="px-6 py-2.5 rounded-xl text-sm font-bold text-white text-center transition-colors duration-150"
                       style="background-color: #D4AF37;"
                       onmouseover="this.style.backgroundColor='#b8972e'"
                       onmouseout="this.style.backgroundColor='#D4AF37'">
                        Volver al panel
                    </a>
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════
             FORMULARIO DE FILTROS
        ══════════════════════════════════════════ --}}
        <div
            x-show="!generado"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="space-y-6">

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

                <div class="px-6 py-5">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

                        {{-- Ciclo --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                Ciclo
                            </label>
                            <select class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700
                                           outline-none transition-all duration-150 bg-white"
                                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                                    onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                                <option value="">Seleccionar</option>
                                @foreach ($ciclos as $ciclo)
                                    <option value="{{ $ciclo }}" {{ $ciclo === '2026-1' ? 'selected' : '' }}>
                                        {{ $ciclo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Cuatrimestre --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                Cuatrimestre
                            </label>
                            <select class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700
                                           outline-none transition-all duration-150 bg-white"
                                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                                    onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                                <option value="">Seleccionar</option>
                                @foreach (range(1, 9) as $n)
                                    <option value="{{ $n }}" {{ $n === 1 ? 'selected' : '' }}>
                                        {{ $n }}°
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Programa --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                Programa
                            </label>
                            <select class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700
                                           outline-none transition-all duration-150 bg-white"
                                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                                    onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                                <option value="">Seleccionar</option>
                                @foreach ($programas as $programa)
                                    <option value="{{ $programa }}" {{ $programa === 'Ingeniería en Sistemas Computacionales' ? 'selected' : '' }}>
                                        {{ $programa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Grupo --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                Grupo
                            </label>
                            <select class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700
                                           outline-none transition-all duration-150 bg-white"
                                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                                    onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                                <option value="">Seleccionar</option>
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo }}" {{ $grupo === 'IS-101' ? 'selected' : '' }}>
                                        {{ $grupo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- Botón Cargar --}}
                    <div class="flex justify-end">
                        <button
                            @click="cargar()"
                            :disabled="cargando"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white
                                   shadow-sm transition-all duration-150"
                            style="background-color: #0F4229;"
                            onmouseover="if(!this.disabled) this.style.backgroundColor='#0a2e1c'"
                            onmouseout="if(!this.disabled) this.style.backgroundColor='#0F4229'">

                            {{-- Spinner --}}
                            <svg x-show="cargando" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"
                                 style="display:none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"></path>
                            </svg>

                            {{-- Ícono normal --}}
                            <svg x-show="!cargando" class="w-4 h-4" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9
                                         m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>

                            <span x-text="cargando ? 'Cargando...' : 'Cargar información'"></span>
                        </button>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════
                 INFO DEL GRUPO (visible tras cargar)
            ══════════════════════════════════════════ --}}
            <div
                x-show="cargado"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6"
                style="display: none;">

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
                                Grupo {{ $infoGrupo['grupo'] }} — {{ $infoGrupo['programa'] }}
                            </h2>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-white"
                              style="background-color: #0F4229;">
                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                            Listo para generar carga
                        </span>
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
                                        {{ $infoGrupo['alumnos'] }}
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
                                        {{ $infoGrupo['materias'] }}
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
                                        {{ $infoGrupo['profesores'] }}
                                    </p>
                                </div>
                            </div>

                        </div>

                        {{-- Info adicional --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                            <div class="bg-uicm-gray rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Ciclo</p>
                                <p class="text-sm font-bold text-gray-800">{{ $infoGrupo['ciclo'] }}</p>
                            </div>
                            <div class="bg-uicm-gray rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Cuatrimestre</p>
                                <p class="text-sm font-bold" style="color: #0F4229;">{{ $infoGrupo['cuatrimestre'] }}</p>
                            </div>
                            <div class="bg-uicm-gray rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Grupo</p>
                                <p class="text-sm font-bold" style="color: #0F4229;">{{ $infoGrupo['grupo'] }}</p>
                            </div>
                            <div class="bg-uicm-gray rounded-xl p-3 text-center">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Estado</p>
                                <span class="inline-flex items-center gap-1 text-xs font-bold"
                                      style="color: #0F4229;">
                                    <span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color: #0F4229;"></span>
                                    Activo
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

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
                        <span class="text-xs text-gray-400">{{ count($materiasGrupo) }} materias</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3">Clave</th>
                                    <th class="px-6 py-3">Materia</th>
                                    <th class="px-6 py-3">Profesor asignado</th>
                                    <th class="px-6 py-3 text-center">Créditos</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($materiasGrupo as $materia)
                                <tr class="hover:bg-gray-50 transition-colors duration-100">
                                    <td class="px-6 py-3 font-mono text-xs font-semibold text-gray-500">
                                        {{ $materia['clave'] }}
                                    </td>
                                    <td class="px-6 py-3 font-semibold text-gray-800">
                                        {{ $materia['nombre'] }}
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">
                                        {{ $materia['profesor'] }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white"
                                              style="background-color: #D4AF37;">
                                            {{ $materia['creditos'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════
                     ACCIÓN PRINCIPAL
                ══════════════════════════════════════════ --}}
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
                            Se asignarán <strong>{{ count($materiasGrupo) }} materias</strong> y
                            <strong>{{ $infoGrupo['profesores'] }} profesores</strong> al grupo
                            <strong>{{ $infoGrupo['grupo'] }}</strong> para el ciclo
                            <strong>{{ $infoGrupo['ciclo'] }}</strong>.
                        </p>

                        <button
                            @click="generar()"
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

                    </div>
                </div>

            </div>{{-- /cargado --}}

        </div>{{-- /!generado --}}

        {{-- Volver --}}
        <div class="mt-6" x-show="!generado">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 text-sm font-medium transition-colors duration-150"
               style="color: #0F4229;"
               onmouseover="this.style.textDecoration='underline'"
               onmouseout="this.style.textDecoration='none'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al panel
            </a>
        </div>

    </div>
</section>

</div>{{-- /x-data --}}

@endsection
