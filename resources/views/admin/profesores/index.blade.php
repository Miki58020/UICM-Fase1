@extends('layouts.app')

@section('title', 'Gestión de Profesores | UICM')

@section('content')

@php
// Datos simulados
$profesores = [
    [
        'id'          => 'PRF-001',
        'nombre'      => 'Dr. Alejandro Fuentes Mora',
        'correo'      => 'a.fuentes@uicm.edu.mx',
        'telefono'    => '222-345-6781',
        'especialidad'=> 'Ingeniería de Software',
        'estado'      => 'activo',
    ],
    [
        'id'          => 'PRF-002',
        'nombre'      => 'Mtra. Carmen Ortiz Salinas',
        'correo'      => 'c.ortiz@uicm.edu.mx',
        'telefono'    => '222-345-6782',
        'especialidad'=> 'Derecho Civil',
        'estado'      => 'activo',
    ],
    [
        'id'          => 'PRF-003',
        'nombre'      => 'Ing. Ricardo Vázquez Pérez',
        'correo'      => 'r.vazquez@uicm.edu.mx',
        'telefono'    => '222-345-6783',
        'especialidad'=> 'Bases de Datos',
        'estado'      => 'activo',
    ],
    [
        'id'          => 'PRF-004',
        'nombre'      => 'Lic. Patricia Herrera Ramos',
        'correo'      => 'p.herrera@uicm.edu.mx',
        'telefono'    => '222-345-6784',
        'especialidad'=> 'Administración Estratégica',
        'estado'      => 'inactivo',
    ],
    [
        'id'          => 'PRF-005',
        'nombre'      => 'Dr. Luis Medina Torres',
        'correo'      => 'l.medina@uicm.edu.mx',
        'telefono'    => '222-345-6785',
        'especialidad'=> 'Cálculo y Álgebra Lineal',
        'estado'      => 'activo',
    ],
    [
        'id'          => 'PRF-006',
        'nombre'      => 'Mtra. Sofía Ríos Castillo',
        'correo'      => 's.rios@uicm.edu.mx',
        'telefono'    => '222-345-6786',
        'especialidad'=> 'Psicología Organizacional',
        'estado'      => 'inactivo',
    ],
    [
        'id'          => 'PRF-007',
        'nombre'      => 'Ing. Marco Antonio Leal Cruz',
        'correo'      => 'm.leal@uicm.edu.mx',
        'telefono'    => '222-345-6787',
        'especialidad'=> 'Redes y Telecomunicaciones',
        'estado'      => 'activo',
    ],
];

$totalActivos   = count(array_filter($profesores, fn($p) => $p['estado'] === 'activo'));
$totalInactivos = count(array_filter($profesores, fn($p) => $p['estado'] === 'inactivo'));
@endphp

<div x-data="{ showModal: false, editando: false }">

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado de módulo --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                    Coordinación Académica
                </p>
                <h1 class="text-2xl font-extrabold text-gray-900">Gestión de Profesores</h1>
                <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
            </div>

            <button
                @click="showModal = true; editando = false"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white
                       shadow-sm transition-colors duration-150 whitespace-nowrap"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2e1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo profesor
            </button>
        </div>

        {{-- Contadores --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total de profesores</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">
                    {{ count($profesores) }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Activos</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">
                    {{ $totalActivos }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Inactivos</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">
                    {{ $totalInactivos }}
                </p>
            </div>

        </div>

        {{-- Card tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Planta docente registrada</h2>
                <span class="text-xs text-gray-400">{{ count($profesores) }} profesores</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Nombre completo</th>
                            <th class="px-6 py-3">Correo institucional</th>
                            <th class="px-6 py-3">Teléfono</th>
                            <th class="px-6 py-3">Especialidad</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach ($profesores as $profesor)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">

                            {{-- ID --}}
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-500 whitespace-nowrap">
                                {{ $profesor['id'] }}
                            </td>

                            {{-- Nombre --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                         style="background-color: #0F4229;">
                                        {{ strtoupper(substr(explode(' ', $profesor['nombre'])[1] ?? $profesor['nombre'], 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-800">{{ $profesor['nombre'] }}</span>
                                </div>
                            </td>

                            {{-- Correo --}}
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $profesor['correo'] }}
                                </span>
                            </td>

                            {{-- Teléfono --}}
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13
                                                 a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498
                                                 a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $profesor['telefono'] }}
                                </span>
                            </td>

                            {{-- Especialidad --}}
                            <td class="px-6 py-4 text-gray-700">
                                {{ $profesor['especialidad'] }}
                            </td>

                            {{-- Estado --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($profesor['estado'] === 'activo')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #9CA3AF;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-60 inline-block"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Editar --}}
                                    <button
                                        @click="showModal = true; editando = true"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white
                                               transition-colors duration-150"
                                        style="background-color: #D4AF37;"
                                        onmouseover="this.style.backgroundColor='#b8972e'"
                                        onmouseout="this.style.backgroundColor='#D4AF37'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                     m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </button>

                                    {{-- Activar / Desactivar --}}
                                    @if ($profesor['estado'] === 'activo')
                                        <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       text-gray-600 bg-gray-100 transition-colors duration-150
                                                       hover:bg-gray-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728
                                                         A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Desactivar
                                        </button>
                                    @else
                                        <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       text-white transition-colors duration-150"
                                                style="background-color: #EFAD5A;"
                                                onmouseover="this.style.backgroundColor='#d4923a'"
                                                onmouseout="this.style.backgroundColor='#EFAD5A'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Activar
                                        </button>
                                    @endif

                                </div>
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

{{-- ══════════════════════════════════════════
     MODAL — Nuevo / Editar profesor
══════════════════════════════════════════ --}}
<div
    x-show="showModal"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @keydown.escape.window="showModal = false"
    class="fixed inset-0 z-50 flex items-center justify-center px-4"
    style="display: none;">

    {{-- Fondo --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
         @click="showModal = false"></div>

    {{-- Panel --}}
    <div
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg z-10"
        @click.stop>

        {{-- Barra superior --}}
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>

        {{-- Cabecera --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-extrabold text-gray-900"
                    x-text="editando ? 'Editar profesor' : 'Nuevo profesor'"></h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Completa los datos del docente
                </p>
            </div>
            <button @click="showModal = false"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Cuerpo --}}
        <div class="px-6 py-5 space-y-4">

            {{-- Nombre completo --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Nombre completo
                </label>
                <input
                    type="text"
                    placeholder="Ej. Dr. Alejandro Fuentes Mora"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800
                           placeholder-gray-400 outline-none transition-all duration-150"
                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                    onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
            </div>

            {{-- Correo institucional --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Correo institucional
                </label>
                <input
                    type="email"
                    placeholder="nombre.apellido@uicm.edu.mx"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800
                           placeholder-gray-400 outline-none transition-all duration-150"
                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                    onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
            </div>

            {{-- Teléfono + Especialidad en fila --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Teléfono
                    </label>
                    <input
                        type="tel"
                        placeholder="222-345-6780"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800
                               placeholder-gray-400 outline-none transition-all duration-150"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                        onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Especialidad
                    </label>
                    <input
                        type="text"
                        placeholder="Ej. Ingeniería de Software"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800
                               placeholder-gray-400 outline-none transition-all duration-150"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                        onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                </div>

            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">

            <button
                @click="showModal = false"
                class="px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100
                       hover:bg-gray-200 transition-colors duration-150">
                Cancelar
            </button>

            <button
                class="px-5 py-2 rounded-xl text-sm font-bold text-white shadow-sm
                       transition-colors duration-150"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2e1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
                Guardar
            </button>

        </div>

    </div>
</div>

</div>{{-- /x-data --}}

@endsection
