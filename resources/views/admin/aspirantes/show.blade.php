@extends('layouts.app')

@section('title', 'Detalle de Aspirante | UICM')

@section('content')

@php
// Datos simulados del aspirante
$aspirante = [
    'folio'           => 'UICM-2026-0001',
    'nombre'          => 'Juan',
    'apellido_pat'    => 'Pérez',
    'apellido_mat'    => 'García',
    'curp'            => 'PEGJ990315HDFRRC09',
    'fecha_nac'       => '15 de marzo de 1999',
    'telefono'        => '+52 55 1234 5678',
    'email'           => 'juan.perez@correo.com',
    'programa'        => 'Ingeniería en Sistemas Computacionales',
    'generacion'      => '2026-A',
    'estado'          => 'pendiente_validacion',
    'fecha_registro'  => '12 de enero de 2026',
];

$documentos = [
    ['nombre' => 'Acta de nacimiento',    'archivo' => 'acta_nacimiento_UICM-2026-0001.pdf',    'estado' => 'subido'],
    ['nombre' => 'Certificado de estudios','archivo' => 'certificado_UICM-2026-0001.pdf',         'estado' => 'subido'],
    ['nombre' => 'Identificación oficial', 'archivo' => 'identificacion_UICM-2026-0001.pdf',      'estado' => 'subido'],
    ['nombre' => 'CURP',                  'archivo' => 'curp_UICM-2026-0001.pdf',                 'estado' => 'pendiente'],
    ['nombre' => 'Fotografía',             'archivo' => 'foto_UICM-2026-0001.jpg',                 'estado' => 'pendiente'],
];
@endphp

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        {{-- ── Navegación de migas ── --}}
        <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
            <a href="{{ route('dashboard') }}" class="hover:text-uicm-green transition-colors">Panel</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('admin.aspirantes.index') }}" class="hover:text-uicm-green transition-colors">Validación de aspirantes</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-600">{{ $aspirante['folio'] }}</span>
        </nav>

        {{-- ── Encabezado del expediente ── --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                        Expediente de aspirante
                    </p>
                    <h1 class="text-xl font-extrabold text-gray-900">
                        {{ $aspirante['nombre'] }} {{ $aspirante['apellido_pat'] }} {{ $aspirante['apellido_mat'] }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $aspirante['folio'] }}</p>
                </div>

                {{-- Badge estado --}}
                <div>
                    @if ($aspirante['estado'] === 'pendiente_validacion')
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-white"
                              style="background-color: #EFAD5A;">
                            <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                            Pendiente de validación
                        </span>
                    @elseif ($aspirante['estado'] === 'validado')
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-white"
                              style="background-color: #0F4229;">
                            <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                            Validado
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-gray-600 bg-gray-200">
                            <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                            Rechazado
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Dos columnas: datos + documentos ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">

            {{-- Columna datos personales (3/5) --}}
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Datos personales</h2>
                </div>

                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Nombre completo</dt>
                            <dd class="text-sm font-semibold text-gray-800">
                                {{ $aspirante['nombre'] }} {{ $aspirante['apellido_pat'] }} {{ $aspirante['apellido_mat'] }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">CURP</dt>
                            <dd class="text-sm font-mono font-semibold text-gray-800 tracking-wide">
                                {{ $aspirante['curp'] }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Fecha de nacimiento</dt>
                            <dd class="text-sm text-gray-800">{{ $aspirante['fecha_nac'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Teléfono</dt>
                            <dd class="text-sm text-gray-800">{{ $aspirante['telefono'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Correo electrónico</dt>
                            <dd class="text-sm text-gray-800">{{ $aspirante['email'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Fecha de registro</dt>
                            <dd class="text-sm text-gray-800">{{ $aspirante['fecha_registro'] }}</dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Programa solicitado</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $aspirante['programa'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Generación</dt>
                            <dd class="text-sm font-bold" style="color: #0F4229;">{{ $aspirante['generacion'] }}</dd>
                        </div>

                    </dl>
                </div>
            </div>

            {{-- Columna documentos (2/5) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                 a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19
                                 a2 2 0 01-2 2z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Documentos</h2>
                </div>

                <ul class="divide-y divide-gray-100">
                    @foreach ($documentos as $doc)
                    <li class="px-6 py-3.5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            {{-- Icono PDF --}}
                            <div class="flex-shrink-0 w-7 h-7 rounded flex items-center justify-center"
                                 style="background-color: #f0f9f4;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="color: #0F4229;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414
                                             A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-700 font-medium truncate">{{ $doc['nombre'] }}</span>
                        </div>

                        @if ($doc['estado'] === 'subido')
                            <a href="#"
                               class="flex-shrink-0 text-xs font-semibold transition-colors duration-150"
                               style="color: #D4AF37;"
                               onmouseover="this.style.color='#b8962e'"
                               onmouseout="this.style.color='#D4AF37'">
                                Ver
                            </a>
                        @else
                            <span class="flex-shrink-0 text-xs text-gray-400 italic">Pendiente</span>
                        @endif
                    </li>
                    @endforeach
                </ul>

                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
                    <p class="text-xs text-gray-400">
                        {{ count(array_filter($documentos, fn($d) => $d['estado'] === 'subido')) }}
                        de {{ count($documentos) }} documentos recibidos
                    </p>
                </div>
            </div>

        </div>{{-- /grid --}}

        {{-- ── Acciones principales ── --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Resolución del expediente</h2>
            </div>
            <div class="px-6 py-5 flex flex-col sm:flex-row items-center gap-4">

                {{-- Aprobar --}}
                <button type="button"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                               px-6 py-3 rounded-xl text-sm font-bold text-white
                               transition-colors duration-200 shadow-sm"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Aprobar aspirante
                </button>

                {{-- Rechazar --}}
                <button type="button"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                               px-6 py-3 rounded-xl text-sm font-bold text-white
                               bg-gray-400 hover:bg-gray-500 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Rechazar aspirante
                </button>

                {{-- Volver a la lista --}}
                <a href="{{ route('admin.aspirantes.index') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                          px-6 py-3 rounded-xl text-sm font-bold border-2
                          transition-colors duration-200"
                   style="border-color: #0F4229; color: #0F4229;"
                   onmouseover="this.style.backgroundColor='#f0f9f4'"
                   onmouseout="this.style.backgroundColor='transparent'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver a la lista
                </a>

            </div>
        </div>{{-- /acciones --}}

    </div>
</section>

@endsection