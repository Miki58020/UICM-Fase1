@extends('layouts.app')

@section('title', 'Portal del Alumno | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen">

    {{-- ══════════════════════════════════════════
         BANNER de bienvenida
    ══════════════════════════════════════════ --}}
    <div class="w-full py-8 px-4" style="background-color: #0F4229;">
        <div class="container mx-auto px-4 lg:px-12 max-w-6xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div class="flex items-center gap-5">
                    {{-- Avatar / Fotografía (con badge para cambiarla) --}}
                    @php
                        $fotoPerfil = auth()->user()->foto ?: ($alumno->aspirante->foto_url ?? null);
                    @endphp
                    <div x-data="{ hover: false }"
                         class="flex-shrink-0 relative cursor-pointer"
                         @mouseenter="hover = true" @mouseleave="hover = false"
                         @click="$refs.fotoInput.click()"
                         title="Cambiar foto de perfil">

                        <div class="w-16 h-16 rounded-2xl border-2 border-white/30 overflow-hidden">
                            @if($fotoPerfil)
                                <img src="{{ route('admin.archivo', ['path' => $fotoPerfil]) }}"
                                     alt="Foto {{ $alumno->nombre }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center
                                            text-2xl font-extrabold text-white select-none"
                                     style="background-color: rgba(255,255,255,0.15);">
                                    {{ strtoupper(substr($alumno->nombre, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Overlay hover (Alpine.js) --}}
                        <div x-show="hover"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute inset-0 rounded-2xl flex flex-col items-center justify-center gap-0.5"
                             style="display:none; background-color: rgba(0,0,0,0.58);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-[10px] font-bold text-white leading-tight text-center">Cambiar<br>foto</span>
                        </div>

                        {{-- Input oculto --}}
                        <form method="POST" action="{{ route('perfil.foto') }}" enctype="multipart/form-data"
                              x-ref="fotoForm" class="hidden">
                            @csrf
                            <input x-ref="fotoInput" type="file" name="foto" accept="image/*"
                                   @change="$refs.fotoForm.submit()">
                        </form>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-white/60 mb-0.5">
                            Portal del Alumno
                        </p>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-white leading-tight">
                            Bienvenido, {{ $alumno->nombre }} {{ $alumno->apellido_paterno }}
                        </h1>
                        <p class="text-sm font-mono text-white/70 mt-0.5">
                            {{ $alumno->matricula }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-start sm:items-end gap-1">
                    <span id="estado-badge-banner"
                          class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold
                                 bg-white/15 text-white border border-white/20">
                        <span id="estado-dot-banner" class="w-2 h-2 rounded-full bg-green-300 inline-block"></span>
                        <span id="estado-text-banner">{{ ucfirst($alumno->estado) }}</span>
                    </span>
                    <p class="text-xs text-white/50">
                        Periodo {{ $alumno->grupo->periodo->nombre ?? '—' }}
                    </p>
                </div>

            </div>
        </div>
    </div>


    {{-- Contenido principal --}}
    <div class="container mx-auto px-4 lg:px-12 max-w-6xl py-8">
        <div class="space-y-6">

            {{-- ══════════════════════════════════════════
                 KPI CARDS
            ══════════════════════════════════════════ --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Materias inscritas --}}
                <a href="{{ route('alumno.kardex') }}"
                   class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col">
                    <div class="h-1.5 w-full flex-shrink-0" style="background-color: #0F4229;"></div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color: #f0f9f4;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                                             C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                                             C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                                             C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-4xl font-extrabold leading-none mb-1" style="color: #0F4229;">{{ $carga->count() }}</p>
                        <p class="text-xs font-medium text-gray-500 mb-3">Materias inscritas</p>
                        <div class="mt-auto flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block" style="background-color: #22c55e;"></span>
                            <span class="text-xs font-medium" style="color: #15803d;">{{ $alumno->cuatrimestre_actual }}° cuatrimestre · {{ $alumno->grupo->clave ?? '—' }}</span>
                        </div>
                    </div>
                </a>

                {{-- Promedio general --}}
                @php
                    $promColor = $promedioGeneral === null ? '#6B7280' : ($promedioGeneral >= 7.0 ? '#0F4229' : '#dc2626');
                    $promBg    = $promedioGeneral === null ? '#f3f4f6' : ($promedioGeneral >= 7.0 ? '#f0f9f4' : '#fef2f2');
                    $promOk    = $promedioGeneral === null ? 'Sin calificaciones aún' : ($promedioGeneral >= 7.0 ? 'Aprobatorio' : 'Por debajo de 7.0');
                    $promDot   = $promedioGeneral === null ? '#9CA3AF' : ($promedioGeneral >= 7.0 ? '#22c55e' : '#dc2626');
                    $promTxt   = $promedioGeneral === null ? '#9CA3AF' : ($promedioGeneral >= 7.0 ? '#15803d' : '#dc2626');
                @endphp
                <a href="{{ route('alumno.kardex') }}"
                   class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col">
                    <div class="h-1.5 w-full flex-shrink-0" style="background-color: {{ $promColor }};"></div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color: {{ $promBg }};">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: {{ $promColor }};">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-4xl font-extrabold leading-none mb-1" style="color: {{ $promColor }};">
                            {{ $promedioGeneral !== null ? number_format($promedioGeneral, 1) : '—' }}
                        </p>
                        <p class="text-xs font-medium text-gray-500 mb-3">Promedio general</p>
                        <div class="mt-auto flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block" style="background-color: {{ $promDot }};"></span>
                            <span class="text-xs font-medium" style="color: {{ $promTxt }};">{{ $promOk }}</span>
                        </div>
                    </div>
                </a>

                {{-- Créditos acumulados --}}
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="h-1.5 w-full flex-shrink-0" style="background-color: #D4AF37;"></div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color: #fdf8ec;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #D4AF37;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0
                                             3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946
                                             3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138
                                             3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806
                                             3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438
                                             3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-4xl font-extrabold leading-none mb-1" style="color: #D4AF37;">
                            {{ $alumno->creditos_acumulados }}
                        </p>
                        <p class="text-xs font-medium text-gray-500 mb-3">Créditos acumulados</p>
                        <div class="mt-auto flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block" style="background-color: #D4AF37;"></span>
                            <span class="text-xs font-medium truncate" style="color: #b45309;">{{ $alumno->programa->nombre ?? 'Programa académico' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Estado de pagos --}}
                @php
                    $pagoAlCorriente = $pagosPendientes === 0;
                    $pagoColor = $pagoAlCorriente ? '#0F4229' : '#dc2626';
                    $pagoBg    = $pagoAlCorriente ? '#f0f9f4' : '#fef2f2';
                    $pagoTexto = $pagoAlCorriente ? 'Al corriente' : ($pagosPendientes === 1 ? '1 pago pendiente' : "$pagosPendientes pagos pendientes");
                    $pagoDot   = $pagoAlCorriente ? '#22c55e' : '#dc2626';
                    $pagoTxt   = $pagoAlCorriente ? '#15803d' : '#dc2626';
                @endphp
                <a href="{{ route('alumno.finanzas.index') }}"
                   class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col">
                    <div class="h-1.5 w-full flex-shrink-0" style="background-color: {{ $pagoColor }};"></div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color: {{ $pagoBg }};">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: {{ $pagoColor }};">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            @if(!$pagoAlCorriente)
                            <span class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0 inline-block" style="background-color: #dc2626;"></span>
                            @endif
                        </div>
                        <p class="text-4xl font-extrabold leading-none mb-1" style="color: {{ $pagoColor }};">
                            {{ $pagoAlCorriente ? '0' : $pagosPendientes }}
                        </p>
                        <p class="text-xs font-medium text-gray-500 mb-3">Pagos pendientes</p>
                        <div class="mt-auto flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block" style="background-color: {{ $pagoDot }};"></span>
                            <span class="text-xs font-medium" style="color: {{ $pagoTxt }};">{{ $pagoTexto }}</span>
                        </div>
                    </div>
                </a>

            </div>


{{-- ══════════════════════════════════════════
                 TABLA: Materias asignadas (ancho completo)
            ══════════════════════════════════════════ --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <a href="{{ route('alumno.materias.index') }}"
                   class="px-6 py-4 border-b border-gray-100 flex items-center gap-2 hover:bg-gray-50 transition-colors duration-100">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                                 C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                                 C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                                 C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700 truncate">Materias</h2>
                </a>
                <div class="overflow-x-auto">
                    @if ($carga->isEmpty())
                        <div class="px-6 py-10 text-center text-sm text-gray-400">No hay materias asignadas.</div>
                    @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Clave</th>
                                <th class="px-6 py-3">Materia</th>
                                <th class="px-6 py-3">Profesor</th>
                                <th class="px-6 py-3 text-center">Créditos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($carga as $c)
                            <tr class="hover:bg-gray-50 transition-colors duration-100">
                                <td class="px-6 py-3.5 font-mono text-xs font-semibold text-gray-500 whitespace-nowrap">
                                    {{ $c->materia->clave }}
                                </td>
                                <td class="px-6 py-3.5 font-medium text-gray-800 whitespace-nowrap">
                                    {{ $c->materia->nombre }}
                                </td>
                                <td class="px-6 py-3.5 text-gray-500 text-xs whitespace-nowrap">
                                    {{ $c->profesor->nombre ?? 'Sin asignar' }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white"
                                          style="background-color: #D4AF37;">
                                        {{ $c->materia->creditos }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>

            {{-- ══════════════════════════════════════════
                 PANELES: calificaciones recientes / documentos / próximo pago
            ══════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Calificaciones recientes --}}
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[350px]">
                    <div class="h-1.5 w-full flex-shrink-0" style="background-color: #0F4229;"></div>
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2 flex-shrink-0">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-gray-700">Calificaciones recientes</h2>
                    </div>
                    <div class="divide-y divide-gray-100 overflow-auto flex-1">
                        @forelse ($calificacionesRecientes as $cal)
                        @php
                            $aprobada = $cal->calificacion >= 7.0;
                        @endphp
                        <div class="px-6 py-3.5 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $cal->cargaAcademica->materia->nombre ?? '—' }}</p>
                                <p class="text-xs text-gray-400 capitalize">{{ $cal->tipo }}</p>
                            </div>
                            <span class="inline-flex items-center flex-shrink-0 px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background-color: {{ $aprobada ? '#f0f9f4' : '#fef2f2' }}; color: {{ $aprobada ? '#0F4229' : '#dc2626' }};">
                                {{ number_format($cal->calificacion, 1) }}
                            </span>
                        </div>
                        @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">Aún no tienes calificaciones registradas.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Documentos por actualizar --}}
                @php
                    $sinPendientesDocs = $documentosAtencion->isEmpty();
                @endphp
                <a href="{{ route('alumno.documentos.index') }}"
                   class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[350px] hover:shadow-md transition-shadow duration-200">
                    <div class="h-1.5 w-full flex-shrink-0" style="background-color: {{ $sinPendientesDocs ? '#0F4229' : '#EFAD5A' }};"></div>
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2 flex-shrink-0">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:{{ $sinPendientesDocs ? '#0F4229' : '#EFAD5A' }};">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-gray-700 truncate">Documentos</h2>
                    </div>
                    <div class="divide-y divide-gray-100 overflow-auto flex-1">
                        @if ($plazoMigradoDocs)
                        <div class="px-6 py-3 text-xs" style="background-color: #fffaf0; color: #b45309;">
                            Tienes hasta el {{ $plazoMigradoDocs->format('d/m/Y') }} para completar tus documentos.
                        </div>
                        @endif
                        @forelse ($documentosAtencion as $item)
                        @php
                            $doc = $item['documento'];
                            $estadoDoc = !$doc ? ['#9ca3af', '#f3f4f6', 'Sin subir']
                                : ($doc->estaVencido() ? ['#dc2626', '#fef2f2', 'Vencido']
                                : ['#EFAD5A', '#fffaf0', 'Por vencer']);
                        @endphp
                        <div class="px-6 py-3.5 flex items-center justify-between gap-3">
                            <p class="text-sm font-medium text-gray-700 truncate">{{ $item['label'] }}</p>
                            <span class="inline-flex items-center flex-shrink-0 px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background-color: {{ $estadoDoc[1] }}; color: {{ $estadoDoc[0] }};">
                                {{ $estadoDoc[2] }}
                            </span>
                        </div>
                        @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">Todos tus documentos están en orden.</div>
                        @endforelse
                    </div>
                </a>

                {{-- Próximo pago --}}
                @php
                    $pagoAtrasado = $proximoPago && method_exists($proximoPago, 'estaVencido') && $proximoPago->estaVencido();
                    $pagoColorPanel = !$proximoPago ? '#0F4229' : ($pagoAtrasado ? '#dc2626' : '#EFAD5A');
                    $pagoBgPanel = !$proximoPago ? '#f0f9f4' : ($pagoAtrasado ? '#fef2f2' : '#fffaf0');
                @endphp
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[350px]">
                    <div class="h-1.5 w-full flex-shrink-0" style="background-color: {{ $pagoColorPanel }};"></div>
                    <a href="{{ route('alumno.finanzas.index') }}"
                       class="px-6 py-4 border-b border-gray-100 flex items-center gap-2 flex-shrink-0 hover:bg-gray-50 transition-colors duration-100">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:{{ $pagoColorPanel }};">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-gray-700 truncate">Próximo pago</h2>
                    </a>
                    <div class="flex-1 flex flex-col">
                        @if ($proximoPago)
                        <div class="px-6 py-5 flex flex-col gap-4 flex-1 justify-center">
                            <div class="flex items-start justify-between">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: {{ $pagoBgPanel }};">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: {{ $pagoColorPanel }};">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-3xl font-extrabold leading-none" style="color: {{ $pagoColorPanel }};">
                                    ${{ number_format($proximoPago->monto, 2) }} <span class="text-sm font-medium text-gray-400">MXN</span>
                                </p>
                                <p class="text-sm font-semibold text-gray-700 mt-2 capitalize">{{ $proximoPago->concepto }}</p>
                                <p class="text-xs text-gray-400">{{ $proximoPago->periodo }}{{ $proximoPago->mes ? ' · '.$proximoPago->mes : '' }}</p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block" style="background-color: {{ $pagoColorPanel }};"></span>
                                <span class="text-xs font-medium" style="color: {{ $pagoColorPanel }};">
                                    {{ $pagoAtrasado ? 'Vencido el' : 'Vence el' }} {{ $proximoPago->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                                </span>
                            </div>
                            <a href="{{ route('alumno.pagos.pagar', $proximoPago) }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-150"
                               style="background-color: #0F4229;"
                               onmouseover="this.style.backgroundColor='#0a2e1c'"
                               onmouseout="this.style.backgroundColor='#0F4229'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Pagar ahora
                            </a>
                        </div>
                        @else
                        <div class="px-6 py-10 text-center text-sm text-gray-400 flex-1 flex items-center justify-center">
                            No tienes pagos pendientes.
                        </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

</section>

{{-- Overlay de cuenta suspendida/baja --}}
<div id="estado-overlay"
     class="hidden fixed inset-0 z-[9999] flex items-center justify-center px-4 bg-black/75 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center">
        <div id="estado-overlay-bar" class="h-1.5 w-full"></div>
        <div class="px-8 py-8">
            <div id="estado-overlay-icon"
                 class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-base font-extrabold text-gray-900 mb-2">Acceso restringido</h2>
            <p id="estado-overlay-msg" class="text-sm text-gray-500 mb-6"></p>
            <p class="text-xs text-gray-400">Serás redirigido al inicio de sesión en unos segundos...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function poll() {
    setTimeout(function () {
        fetch('{{ route('alumno.check-estado') }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.ok ? r.json() : null)
        .then(function (data) {
            if (!data) { poll(); return; }

            if (data.estado !== 'activo') {
                // Actualizar badge del banner
                const bannerText = document.getElementById('estado-text-banner');
                const label = data.estado === 'baja' ? 'Baja' : 'Inactivo';
                if (bannerText) bannerText.textContent = label;

                // Mostrar overlay
                const msg = data.estado === 'baja'
                    ? 'Tu cuenta ha sido dada de baja. Comunícate con Control Escolar para más información.'
                    : 'Tu cuenta está inactiva temporalmente. Comunícate con Control Escolar para reactivarla.';

                const color = data.estado === 'baja' ? '#b91c1c' : '#854d0e';
                document.getElementById('estado-overlay-bar').style.backgroundColor   = color;
                document.getElementById('estado-overlay-icon').style.backgroundColor  = color;
                document.getElementById('estado-overlay-msg').textContent             = msg;
                document.getElementById('estado-overlay').classList.remove('hidden');

                setTimeout(() => {
                    window.close();
                    // Fallback si el navegador bloquea el cierre de pestaña
                    setTimeout(() => { window.location.replace('{{ route('login') }}'); }, 400);
                }, 5000);
            } else {
                poll(); // Seguir revisando
            }
        })
        .catch(function () { poll(); }); // Reintentar si hay error de red
    }, 30000); // Verificar cada 30 segundos
})();
</script>
@endpush

@endsection
