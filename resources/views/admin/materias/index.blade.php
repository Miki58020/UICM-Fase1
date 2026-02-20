@extends('layouts.app')

@section('title', 'Gestión de Materias | UICM')

@section('content')

@php
// Datos simulados
$materias = [
    [
        'clave'       => 'IS-101',
        'nombre'      => 'Programación I',
        'programa'    => 'Ingeniería en Sistemas',
        'cuatrimestre'=> '1°',
        'creditos'    => 8,
        'estado'      => 'activa',
    ],
    [
        'clave'       => 'IS-102',
        'nombre'      => 'Matemáticas Discretas',
        'programa'    => 'Ingeniería en Sistemas',
        'cuatrimestre'=> '1°',
        'creditos'    => 8,
        'estado'      => 'activa',
    ],
    [
        'clave'       => 'IS-201',
        'nombre'      => 'Estructuras de Datos',
        'programa'    => 'Ingeniería en Sistemas',
        'cuatrimestre'=> '2°',
        'creditos'    => 10,
        'estado'      => 'activa',
    ],
    [
        'clave'       => 'DER-101',
        'nombre'      => 'Introducción al Derecho',
        'programa'    => 'Derecho',
        'cuatrimestre'=> '1°',
        'creditos'    => 8,
        'estado'      => 'activa',
    ],
    [
        'clave'       => 'ADM-102',
        'nombre'      => 'Contabilidad General',
        'programa'    => 'Administración de Empresas',
        'cuatrimestre'=> '1°',
        'creditos'    => 6,
        'estado'      => 'inactiva',
    ],
    [
        'clave'       => 'IS-301',
        'nombre'      => 'Bases de Datos',
        'programa'    => 'Ingeniería en Sistemas',
        'cuatrimestre'=> '3°',
        'creditos'    => 10,
        'estado'      => 'activa',
    ],
    [
        'clave'       => 'PSI-101',
        'nombre'      => 'Psicología General',
        'programa'    => 'Psicología',
        'cuatrimestre'=> '1°',
        'creditos'    => 8,
        'estado'      => 'inactiva',
    ],
];

$programas = [
    'Ingeniería en Sistemas Computacionales',
    'Derecho',
    'Administración de Empresas',
    'Psicología',
    'Contaduría Pública',
];
@endphp

{{-- Alpine.js scope: controla modal --}}
<section
    x-data="{ showModal: false }"
    class="bg-uicm-gray min-h-screen py-12 px-4">

    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- ── Encabezado + botón ── --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                    Coordinación Académica
                </p>
                <h1 class="text-2xl font-extrabold text-gray-900">Gestión de Materias</h1>
                <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
            </div>

            {{-- Botón Nueva materia --}}
            <button
                type="button"
                @click="showModal = true"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold
                       text-white shadow-sm transition-colors duration-200 self-start sm:self-auto"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2e1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva materia
            </button>
        </div>

        {{-- ── Contadores ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total registradas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ count($materias) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Activas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">
                    {{ count(array_filter($materias, fn($m) => $m['estado'] === 'activa')) }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 col-span-2 sm:col-span-1" style="border-color: #9ca3af;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Inactivas</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">
                    {{ count(array_filter($materias, fn($m) => $m['estado'] === 'inactiva')) }}
                </p>
            </div>
        </div>

        {{-- ── Card tabla ── --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Catálogo de materias</h2>
                <span class="text-xs text-gray-400">{{ count($materias) }} registros</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Clave</th>
                            <th class="px-6 py-3">Nombre de la materia</th>
                            <th class="px-6 py-3">Programa académico</th>
                            <th class="px-6 py-3 text-center">Cuatrimestre</th>
                            <th class="px-6 py-3 text-center">Créditos</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach ($materias as $materia)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">

                            {{-- Clave --}}
                            <td class="px-6 py-4 font-mono text-xs font-bold whitespace-nowrap"
                                style="color: #0F4229;">
                                {{ $materia['clave'] }}
                            </td>

                            {{-- Nombre --}}
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                {{ $materia['nombre'] }}
                            </td>

                            {{-- Programa --}}
                            <td class="px-6 py-4 text-gray-600 text-xs">
                                {{ $materia['programa'] }}
                            </td>

                            {{-- Cuatrimestre --}}
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white"
                                      style="background-color: #D4AF37;">
                                    {{ $materia['cuatrimestre'] }}
                                </span>
                            </td>

                            {{-- Créditos --}}
                            <td class="px-6 py-4 text-center font-bold text-gray-700">
                                {{ $materia['creditos'] }}
                            </td>

                            {{-- Estado --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($materia['estado'] === 'activa')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold text-white"
                                          style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold text-gray-600 bg-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                        Inactiva
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Editar --}}
                                    <button
                                        type="button"
                                        @click="showModal = true"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                               text-xs font-semibold text-white transition-colors duration-150"
                                        style="background-color: #D4AF37;"
                                        onmouseover="this.style.backgroundColor='#b8962e'"
                                        onmouseout="this.style.backgroundColor='#D4AF37'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                     m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </button>

                                    {{-- Activar / Desactivar --}}
                                    @if ($materia['estado'] === 'activa')
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                                   text-xs font-semibold text-white bg-gray-400
                                                   hover:bg-gray-500 transition-colors duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728
                                                         A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Desactivar
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                                   text-xs font-semibold text-white transition-colors duration-150"
                                            style="background-color: #EFAD5A;"
                                            onmouseover="this.style.backgroundColor='#e09a3a'"
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

    </div>{{-- /container --}}


    {{-- ════════════════════════════════════════════════
         MODAL — Nueva materia / Editar materia
         Controlado por Alpine.js
    ════════════════════════════════════════════════ --}}
    <div
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto"
        style="background-color: rgba(0,0,0,0.5);"
        @keydown.escape.window="showModal = false"
        x-cloak>

        <div
            @click.outside="showModal = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl w-full max-w-lg my-auto">

            {{-- Header modal --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold" style="color: #0F4229;">Nueva materia</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Completa los campos del formulario</p>
                </div>
                <button
                    type="button"
                    @click="showModal = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors duration-150"
                    aria-label="Cerrar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Formulario --}}
            <form class="px-6 py-5 space-y-4">

                {{-- Fila: Clave + Créditos --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="m_clave" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                            Clave <span class="text-red-400">*</span>
                        </label>
                        <input
                            type="text"
                            id="m_clave"
                            name="clave"
                            placeholder="IS-101"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono
                                   focus:outline-none transition-colors duration-150"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label for="m_creditos" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                            Créditos <span class="text-red-400">*</span>
                        </label>
                        <input
                            type="number"
                            id="m_creditos"
                            name="creditos"
                            min="1"
                            max="20"
                            placeholder="8"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                   focus:outline-none transition-colors duration-150"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    </div>
                </div>

                {{-- Nombre --}}
                <div>
                    <label for="m_nombre" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Nombre de la materia <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="m_nombre"
                        name="nombre"
                        placeholder="Ej. Programación I"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                               focus:outline-none transition-colors duration-150"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>

                {{-- Programa --}}
                <div>
                    <label for="m_programa" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Programa académico <span class="text-red-400">*</span>
                    </label>
                    <select
                        id="m_programa"
                        name="programa"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white
                               focus:outline-none transition-colors duration-150"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="" disabled selected>Selecciona un programa</option>
                        @foreach ($programas as $programa)
                            <option value="{{ $programa }}">{{ $programa }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Cuatrimestre --}}
                <div>
                    <label for="m_cuatrimestre" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Cuatrimestre <span class="text-red-400">*</span>
                    </label>
                    <select
                        id="m_cuatrimestre"
                        name="cuatrimestre"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white
                               focus:outline-none transition-colors duration-150"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="" disabled selected>Selecciona el cuatrimestre</option>
                        @foreach (range(1, 9) as $n)
                            <option value="{{ $n }}°">{{ $n }}°</option>
                        @endforeach
                    </select>
                </div>

                {{-- Footer modal --}}
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300
                               rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        class="px-5 py-2 text-sm font-bold text-white rounded-lg
                               transition-colors duration-200 shadow-sm"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                        Guardar materia
                    </button>
                </div>

            </form>
        </div>{{-- /panel --}}
    </div>{{-- /modal --}}

</section>

@endsection
