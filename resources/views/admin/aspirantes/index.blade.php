@extends('layouts.app')

@section('title', 'Validación de Aspirantes | UICM')

@section('content')

@php
// Datos simulados
$aspirantes = [
    [
        'folio'    => 'UICM-2026-0001',
        'nombre'   => 'Juan Pérez García',
        'programa' => 'Ingeniería en Sistemas',
        'estado'   => 'pendiente_validacion',
    ],
    [
        'folio'    => 'UICM-2026-0002',
        'nombre'   => 'María López Torres',
        'programa' => 'Derecho',
        'estado'   => 'pendiente_validacion',
    ],
    [
        'folio'    => 'UICM-2026-0003',
        'nombre'   => 'Carlos Ruiz Mendoza',
        'programa' => 'Administración de Empresas',
        'estado'   => 'validado',
    ],
    [
        'folio'    => 'UICM-2026-0004',
        'nombre'   => 'Ana Martínez Soto',
        'programa' => 'Psicología',
        'estado'   => 'rechazado',
    ],
    [
        'folio'    => 'UICM-2026-0005',
        'nombre'   => 'Roberto Hernández Luna',
        'programa' => 'Contaduría Pública',
        'estado'   => 'pendiente_validacion',
    ],
    [
        'folio'    => 'UICM-2026-0006',
        'nombre'   => 'Sofía Ramírez Vega',
        'programa' => 'Ingeniería en Sistemas',
        'estado'   => 'validado',
    ],
];
@endphp

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- ── Encabezado de módulo ── --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Control Escolar
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Validación de Aspirantes</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- ── Contadores rápidos ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pendientes</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">
                    {{ count(array_filter($aspirantes, fn($a) => $a['estado'] === 'pendiente_validacion')) }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Validados</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">
                    {{ count(array_filter($aspirantes, fn($a) => $a['estado'] === 'validado')) }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 col-span-2 sm:col-span-1" style="border-color: #9ca3af;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Rechazados</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">
                    {{ count(array_filter($aspirantes, fn($a) => $a['estado'] === 'rechazado')) }}
                </p>
            </div>

        </div>

        {{-- ── Card tabla ── --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            {{-- Barra superior verde --}}
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            {{-- Cabecera card --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">
                    Lista de aspirantes — Generación 2026
                </h2>
                <span class="text-xs text-gray-400">
                    {{ count($aspirantes) }} registros
                </span>
            </div>

            {{-- Tabla con scroll horizontal en móvil --}}
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

                            {{-- Folio --}}
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-600 whitespace-nowrap">
                                {{ $aspirante['folio'] }}
                            </td>

                            {{-- Nombre --}}
                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                                {{ $aspirante['nombre'] }}
                            </td>

                            {{-- Programa --}}
                            <td class="px-6 py-4 text-gray-600">
                                {{ $aspirante['programa'] }}
                            </td>

                            {{-- Badge de estado --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($aspirante['estado'] === 'pendiente_validacion')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #EFAD5A;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Pendiente
                                    </span>
                                @elseif ($aspirante['estado'] === 'validado')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Validado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                        Rechazado
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2 flex-wrap">

                                    {{-- Ver --}}
                                    <a href="{{ route('admin.aspirantes.show') }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150 whitespace-nowrap"
                                       style="background-color: #D4AF37;"
                                       onmouseover="this.style.backgroundColor='#b8962e'"
                                       onmouseout="this.style.backgroundColor='#D4AF37'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                                     -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>

                                    {{-- Aprobar --}}
                                    <button type="button"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150 whitespace-nowrap"
                                            style="background-color: #0F4229;"
                                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                                            onmouseout="this.style.backgroundColor='#0F4229'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Aprobar
                                    </button>

                                    {{-- Rechazar --}}
                                    <button type="button"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-gray-400 transition-colors duration-150 hover:bg-gray-500 whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Rechazar
                                    </button>

                                </div>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
            

        </div>{{-- /card --}}

        {{-- Volver al dashboard --}}
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