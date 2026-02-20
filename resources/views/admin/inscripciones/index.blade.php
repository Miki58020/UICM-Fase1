@extends('layouts.app')

@section('title', 'Generación de Matrículas | UICM')

@section('content')

@php
// Datos simulados — aspirantes con pago validado listos para inscripción
$aspirantes = [
    [
        'folio'    => 'UICM-2026-0001',
        'nombre'   => 'Juan Pérez García',
        'programa' => 'Ingeniería en Sistemas',
        'estado'   => 'pago_validado',
    ],
    [
        'folio'    => 'UICM-2026-0002',
        'nombre'   => 'María López Torres',
        'programa' => 'Derecho',
        'estado'   => 'pago_validado',
    ],
    [
        'folio'    => 'UICM-2026-0003',
        'nombre'   => 'Carlos Ruiz Mendoza',
        'programa' => 'Administración de Empresas',
        'estado'   => 'pago_validado',
    ],
    [
        'folio'    => 'UICM-2026-0004',
        'nombre'   => 'Sofía Ramírez Vega',
        'programa' => 'Ingeniería en Sistemas',
        'estado'   => 'pago_validado',
    ],
    [
        'folio'    => 'UICM-2026-0005',
        'nombre'   => 'Roberto Hernández Luna',
        'programa' => 'Contaduría Pública',
        'estado'   => 'pago_validado',
    ],
];
@endphp

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado de módulo --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Control Escolar
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Aspirantes listos para inscripción</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Contador --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Listos para inscribir</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">
                    {{ count($aspirantes) }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pago validado</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">
                    {{ count(array_filter($aspirantes, fn($a) => $a['estado'] === 'pago_validado')) }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Matrículas generadas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">0</p>
            </div>

        </div>

        {{-- Card tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">
                    Aspirantes aprobados — Generación 2026
                </h2>
                <span class="text-xs text-gray-400">{{ count($aspirantes) }} registros</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Folio</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach ($aspirantes as $aspirante)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">

                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-600 whitespace-nowrap">
                                {{ $aspirante['folio'] }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                                {{ $aspirante['nombre'] }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $aspirante['programa'] }}
                            </td>

                            {{-- Badge pago validado --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                      style="background-color: #0F4229;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                    Pago validado
                                </span>
                            </td>

                            {{-- Acción --}}
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.inscripciones.generar') }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-white
                                          transition-colors duration-150 whitespace-nowrap"
                                   style="background-color: #0F4229;"
                                   onmouseover="this.style.backgroundColor='#0a2e1c'"
                                   onmouseout="this.style.backgroundColor='#0F4229'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                                 a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964
                                                 A6 6 0 1121 9z"/>
                                    </svg>
                                    Generar matrícula
                                </a>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>


        </div>{{-- /card --}}

        {{-- Volver --}}
        <div class="mt-6">
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

@endsection
