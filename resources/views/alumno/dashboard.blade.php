@extends('layouts.app')

@section('title', 'Portal del Alumno | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen" x-data="{ modalContrasena: false }">

    {{-- ══════════════════════════════════════════
         BANNER de bienvenida
    ══════════════════════════════════════════ --}}
    <div class="w-full py-8 px-4" style="background-color: #0F4229;">
        <div class="container mx-auto px-4 lg:px-12 max-w-6xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div class="flex items-center gap-5">
                    {{-- Avatar --}}
                    <div class="flex-shrink-0 w-16 h-16 rounded-2xl flex items-center justify-center
                                text-2xl font-extrabold text-white border-2 border-white/30"
                         style="background-color: rgba(255,255,255,0.15);">
                        {{ strtoupper(substr($alumno->nombre, 0, 1)) }}
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
                        {{ $alumno->matricula }}
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
                        {{ $alumno->programa->nombre ?? '—' }}
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
                    <p class="text-lg font-extrabold" style="color: #0F4229;">
                        {{ $alumno->grupo->clave ?? 'Sin grupo' }}
                    </p>
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
                    <p class="text-lg font-extrabold" style="color: #EFAD5A;">
                        {{ $alumno->cuatrimestre_actual }}°
                    </p>
                </div>

            </div>

            {{-- Fila 2: Estado + resumen --}}
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
                        <span id="estado-badge-card"
                              class="inline-flex items-center gap-1.5 mt-1 px-3 py-1 rounded-full text-xs font-bold text-white"
                              style="background-color: #0F4229;">
                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                            <span id="estado-text-card">{{ ucfirst($alumno->estado) }}</span>
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
                            {{ $carga->count() }}
                        </p>
                    </div>
                </div>

                {{-- Cambiar contraseña --}}
                <button type="button" @click="modalContrasena = true"
                        class="bg-white rounded-2xl shadow-sm overflow-hidden flex items-center gap-4 px-5 py-4
                               w-full text-left hover:shadow-md transition-shadow duration-150 cursor-pointer">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: #fef4e8;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #EFAD5A;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                     a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Contraseña</p>
                        <p class="text-sm font-bold mt-0.5" style="color: #EFAD5A;">Cambiar</p>
                    </div>
                </button>

            </div>

            {{-- Flash contraseña actualizada --}}
            @if (session('password_success'))
            <div class="rounded-xl px-5 py-3 border-l-4 bg-green-50 flex items-center gap-3"
                 style="border-color: #0F4229;">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: #0F4229;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm font-semibold text-green-800">{{ session('password_success') }}</p>
            </div>
            @endif

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
                    <span class="ml-auto text-xs text-gray-400">
                        {{ $carga->count() }} materias — Periodo {{ $alumno->grupo->periodo->nombre ?? '—' }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    @if ($carga->isEmpty())
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            No hay materias asignadas. El grupo aún no tiene carga académica registrada.
                        </div>
                    @else
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
                            @foreach ($carga as $c)
                            <tr class="hover:bg-gray-50 transition-colors duration-100">

                                <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-500 whitespace-nowrap">
                                    {{ $c->materia->clave }}
                                </td>

                                <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                    {{ $c->materia->nombre }}
                                </td>

                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                    {{ $c->profesor->nombre }}
                                </td>

                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $c->horario ?? '—' }}
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
                                        {{ $c->aula ?? '—' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
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
                    <span class="ml-auto text-xs text-gray-400">{{ $pagos->count() }} registros</span>
                </div>

                {{-- Banner de estado de pago --}}
                @php $pagoInscripcion = $pagos->where('concepto', 'inscripcion')->first(); @endphp
                @if($pagoInscripcion)
                <div class="px-6 py-3 flex items-center gap-3 border-b border-gray-100
                    {{ $pagoInscripcion->estado === 'aprobado' ? 'bg-green-50' : ($pagoInscripcion->estado === 'pendiente' ? 'bg-yellow-50' : 'bg-red-50') }}">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0
                        {{ $pagoInscripcion->estado === 'aprobado' ? 'bg-green-500' : ($pagoInscripcion->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-500') }}">
                    </span>
                    <span class="text-sm font-medium
                        {{ $pagoInscripcion->estado === 'aprobado' ? 'text-green-800' : ($pagoInscripcion->estado === 'pendiente' ? 'text-yellow-800' : 'text-red-800') }}">
                        @if($pagoInscripcion->estado === 'aprobado')
                            Pago de inscripción completado
                        @elseif($pagoInscripcion->estado === 'pendiente')
                            Pago de inscripción en revisión
                        @else
                            Pago de inscripción rechazado
                        @endif
                    </span>
                </div>
                @endif

                <div class="overflow-x-auto">
                    @if ($pagos->isEmpty())
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            Sin registros de pago.
                        </div>
                    @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Concepto</th>
                                <th class="px-6 py-3">Periodo</th>
                                <th class="px-6 py-3">Monto</th>
                                <th class="px-6 py-3">Fecha</th>
                                <th class="px-6 py-3">Estado</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($pagos as $pago)
                            <tr class="hover:bg-gray-50 transition-colors duration-100">

                                <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap capitalize">
                                    {{ $pago->concepto }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                        {{ $pago->periodo }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-bold text-gray-800 whitespace-nowrap">
                                    ${{ number_format($pago->monto, 0) }} MXN
                                </td>

                                <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                                    @if ($pago->fecha_pago)
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $pago->fecha_pago->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($pago->estado === 'aprobado')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                              style="background-color: #0F4229;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                            Pagado
                                        </span>
                                    @elseif ($pago->estado === 'rechazado')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                            Rechazado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                              style="background-color: #EFAD5A;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                            En revisión
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($pago->aspirante_id)
                                    <a href="{{ route('alumno.comprobante', $pago) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors"
                                       style="background-color: #0F4229;"
                                       onmouseover="this.style.backgroundColor='#0a2e1c'"
                                       onmouseout="this.style.backgroundColor='#0F4229'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                                     a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Comprobante
                                    </a>
                                    @else
                                    <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>


        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: Cambiar contraseña
    ══════════════════════════════════════════ --}}
    <div x-show="modalContrasena"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background-color: rgba(0,0,0,0.5); display: none;"
         @click.self="modalContrasena = false">

        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #EFAD5A;"></div>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #EFAD5A;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                 a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <h2 class="text-sm font-bold text-gray-800">Cambiar contraseña</h2>
                </div>
                <button type="button" @click="modalContrasena = false"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400
                               hover:text-gray-600 hover:bg-gray-100 transition-colors duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('alumno.cambiar-password') }}" class="px-6 py-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Nueva contraseña <span class="normal-case font-normal text-gray-400">(mín. 8 caracteres)</span>
                    </label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 text-sm border rounded-xl bg-white focus:outline-none
                                  @error('password') border-red-400 @else border-gray-300 @enderror"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                           onblur="this.style.borderColor=''; this.style.boxShadow=''">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Confirmar contraseña
                    </label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                           onblur="this.style.borderColor=''; this.style.boxShadow=''">
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="modalContrasena = false"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold border-2 transition-colors duration-150"
                            style="border-color: #0F4229; color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#f0f9f4'"
                            onmouseout="this.style.backgroundColor='transparent'">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-200"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        Guardar
                    </button>
                </div>
            </form>

        </div>
    </div>

</section>

{{-- Overlay de cuenta suspendida/baja --}}
<div id="estado-overlay"
     class="hidden fixed inset-0 z-[9999] flex items-center justify-center px-4"
     style="background-color: rgba(0,0,0,0.75);">
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
                // Actualizar badges
                const textos  = ['estado-text-banner', 'estado-text-card'];
                const label   = data.estado === 'baja' ? 'Baja' : 'Inactivo';
                textos.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = label;
                });

                const cardBadge = document.getElementById('estado-badge-card');
                if (cardBadge) cardBadge.style.backgroundColor = data.estado === 'baja' ? '#b91c1c' : '#854d0e';

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
