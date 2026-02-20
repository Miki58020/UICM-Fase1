@extends('layouts.app')

@section('title', 'Portal del Alumno | UICM')

@section('content')

@php
// Datos simulados del alumno
$alumno = [
    'nombre'        => 'Juan Pérez García',
    'matricula'     => 'UICM-2026-IS-001',
    'programa'      => 'Ingeniería en Sistemas Computacionales',
    'grupo'         => 'IS-101',
    'cuatrimestre'  => '1°',
    'ciclo'         => '2026-1',
    'email'         => 'juan.perez@uicm.edu.mx',
    'estado'        => 'Activo',
];

$materias = [
    [
        'clave'    => 'IS-101',
        'nombre'   => 'Programación I',
        'profesor' => 'Dr. Alejandro Fuentes Mora',
        'horario'  => 'Lun-Mié 8:00-10:00',
        'aula'     => 'A-201',
        'creditos' => 8,
    ],
    [
        'clave'    => 'MAT-101',
        'nombre'   => 'Cálculo Diferencial',
        'profesor' => 'Dr. Luis Medina Torres',
        'horario'  => 'Mar-Jue 10:00-12:00',
        'aula'     => 'B-105',
        'creditos' => 8,
    ],
    [
        'clave'    => 'FIS-101',
        'nombre'   => 'Física I',
        'profesor' => 'Ing. Ricardo Vázquez Pérez',
        'horario'  => 'Vie 8:00-10:00',
        'aula'     => 'A-301',
        'creditos' => 6,
    ],
    [
        'clave'    => 'HUM-101',
        'nombre'   => 'Habilidades del Pensamiento',
        'profesor' => 'Mtra. Carmen Ortiz Salinas',
        'horario'  => 'Lun 12:00-14:00',
        'aula'     => 'C-102',
        'creditos' => 4,
    ],
    [
        'clave'    => 'ADM-101',
        'nombre'   => 'Introducción a la Administración',
        'profesor' => 'Lic. Patricia Herrera Ramos',
        'horario'  => 'Mié 12:00-14:00',
        'aula'     => 'B-203',
        'creditos' => 4,
    ],
    [
        'clave'    => 'ING-101',
        'nombre'   => 'Inglés I',
        'profesor' => 'Mtra. Sofía Ríos Castillo',
        'horario'  => 'Jue 14:00-16:00',
        'aula'     => 'A-105',
        'creditos' => 4,
    ],
];

$pagos = [
    [
        'concepto' => 'Inscripción',
        'periodo'  => '2026-1',
        'monto'    => '$3,500 MXN',
        'fecha'    => '15/02/2026',
        'estado'   => 'pagado',
    ],
    [
        'concepto' => 'Reinscripción',
        'periodo'  => '2025-2',
        'monto'    => '$2,800 MXN',
        'fecha'    => '18/10/2025',
        'estado'   => 'pagado',
    ],
    [
        'concepto' => 'Reinscripción',
        'periodo'  => '2026-2',
        'monto'    => '$2,800 MXN',
        'fecha'    => '—',
        'estado'   => 'pendiente',
    ],
];

$totalCreditos = array_sum(array_column($materias, 'creditos'));
@endphp

<section class="bg-uicm-gray min-h-screen">

    {{-- ══════════════════════════════════════════
         BANNER de bienvenida
    ══════════════════════════════════════════ --}}
    <div class="w-full py-8 px-4" style="background-color: #0F4229;">
        <div class="container mx-auto px-4 lg:px-12 max-w-6xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div class="flex items-center gap-5">
                    {{-- Avatar grande --}}
                    <div class="flex-shrink-0 w-16 h-16 rounded-2xl flex items-center justify-center
                                text-2xl font-extrabold text-white border-2 border-white/30"
                         style="background-color: rgba(255,255,255,0.15);">
                        {{ strtoupper(substr($alumno['nombre'], 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-white/60 mb-0.5">
                            Portal del Alumno
                        </p>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-white leading-tight">
                            Bienvenido, {{ explode(' ', $alumno['nombre'])[0] }} {{ explode(' ', $alumno['nombre'])[1] }}
                        </h1>
                        <p class="text-sm font-mono text-white/70 mt-0.5">
                            {{ $alumno['matricula'] }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-start sm:items-end gap-1">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold
                                 bg-white/15 text-white border border-white/20">
                        <span class="w-2 h-2 rounded-full bg-green-300 inline-block"></span>
                        {{ $alumno['estado'] }}
                    </span>
                    <p class="text-xs text-white/50">Ciclo {{ $alumno['ciclo'] }}</p>
                </div>

            </div>
        </div>
    </div>

    {{-- Contenido principal --}}
    <div class="container mx-auto px-4 lg:px-12 max-w-6xl py-8">
        <div class="space-y-6">

            {{-- ══════════════════════════════════════════
                 CARDS: Información académica
            ══════════════════════════════════════════ --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">

                {{-- Matrícula --}}
                <div class="bg-white rounded-2xl shadow-sm px-4 py-4 flex flex-col items-center text-center
                            border-t-4" style="border-color: #0F4229;">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3"
                         style="background-color: #f0f9f4;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5
                                     m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Matrícula</p>
                    <p class="text-xs font-extrabold font-mono leading-tight" style="color: #0F4229;">
                        {{ $alumno['matricula'] }}
                    </p>
                </div>

                {{-- Programa --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm px-4 py-4 flex flex-col items-center text-center
                            border-t-4" style="border-color: #D4AF37;">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3"
                         style="background-color: #fdf8ec;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #D4AF37;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479
                                     A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998
                                     12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Programa</p>
                    <p class="text-xs font-bold text-gray-800 leading-snug">
                        {{ $alumno['programa'] }}
                    </p>
                </div>

                {{-- Grupo --}}
                <div class="bg-white rounded-2xl shadow-sm px-4 py-4 flex flex-col items-center text-center
                            border-t-4" style="border-color: #0F4229;">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3"
                         style="background-color: #f0f9f4;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                                     M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                                     m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Grupo</p>
                    <p class="text-lg font-extrabold" style="color: #0F4229;">{{ $alumno['grupo'] }}</p>
                </div>

                {{-- Cuatrimestre --}}
                <div class="bg-white rounded-2xl shadow-sm px-4 py-4 flex flex-col items-center text-center
                            border-t-4" style="border-color: #EFAD5A;">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3"
                         style="background-color: #fef4e8;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #EFAD5A;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Cuatrimestre</p>
                    <p class="text-lg font-extrabold" style="color: #EFAD5A;">{{ $alumno['cuatrimestre'] }}</p>
                </div>

            </div>

            {{-- Fila 2: Estado + resumen créditos --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- Estado administrativo --}}
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex items-center gap-4 px-5 py-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: #f0f9f4;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
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
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Estado administrativo</p>
                        <span class="inline-flex items-center gap-1.5 mt-1 px-3 py-1 rounded-full text-xs font-bold text-white"
                              style="background-color: #0F4229;">
                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                            {{ $alumno['estado'] }}
                        </span>
                    </div>
                </div>

                {{-- Total materias --}}
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex items-center gap-4 px-5 py-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
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
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Materias inscritas</p>
                        <p class="text-2xl font-extrabold mt-0.5" style="color: #D4AF37;">
                            {{ count($materias) }}
                        </p>
                    </div>
                </div>

                {{-- Total créditos --}}
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex items-center gap-4 px-5 py-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: #fef4e8;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #EFAD5A;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915
                                     c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674
                                     c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888
                                     c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888
                                     c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Créditos totales</p>
                        <p class="text-2xl font-extrabold mt-0.5" style="color: #EFAD5A;">
                            {{ $totalCreditos }}
                        </p>
                    </div>
                </div>

            </div>

            {{-- ══════════════════════════════════════════
                 TABLA: Materias asignadas
            ══════════════════════════════════════════ --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                                 C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                                 C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                                 C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Materias asignadas</h2>
                    <span class="ml-auto text-xs text-gray-400">{{ count($materias) }} materias — Ciclo {{ $alumno['ciclo'] }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Clave</th>
                                <th class="px-6 py-3">Materia</th>
                                <th class="px-6 py-3">Profesor</th>
                                <th class="px-6 py-3">Horario</th>
                                <th class="px-6 py-3">Aula</th>
                                <th class="px-6 py-3 text-center">Créditos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($materias as $materia)
                            <tr class="hover:bg-gray-50 transition-colors duration-100">

                                <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-500 whitespace-nowrap">
                                    {{ $materia['clave'] }}
                                </td>

                                <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                    {{ $materia['nombre'] }}
                                </td>

                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                    {{ $materia['profesor'] }}
                                </td>

                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $materia['horario'] }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 text-gray-600">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3
                                                     m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        {{ $materia['aula'] }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
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
                 TABLA: Historial de pagos
            ══════════════════════════════════════════ --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #D4AF37;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10
                                 a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Historial de pagos</h2>
                    <span class="ml-auto text-xs text-gray-400">{{ count($pagos) }} registros</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Concepto</th>
                                <th class="px-6 py-3">Periodo</th>
                                <th class="px-6 py-3">Monto</th>
                                <th class="px-6 py-3">Fecha</th>
                                <th class="px-6 py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($pagos as $pago)
                            <tr class="hover:bg-gray-50 transition-colors duration-100">

                                <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                                    {{ $pago['concepto'] }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                        {{ $pago['periodo'] }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-bold text-gray-800 whitespace-nowrap">
                                    {{ $pago['monto'] }}
                                </td>

                                <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                                    @if ($pago['fecha'] !== '—')
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $pago['fecha'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($pago['estado'] === 'pagado')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                              style="background-color: #0F4229;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                            Pagado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                              style="background-color: #EFAD5A;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                            Pendiente
                                        </span>
                                    @endif
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Volver --}}
            <div>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 text-sm font-medium transition-colors duration-150"
                   style="color: #0F4229;"
                   onmouseover="this.style.textDecoration='underline'"
                   onmouseout="this.style.textDecoration='none'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver al inicio
                </a>
            </div>

        </div>
    </div>

</section>

@endsection
