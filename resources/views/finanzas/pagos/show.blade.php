@extends('layouts.app')

@section('title', 'Detalle de Pago | UICM')

@section('content')

@php
// Datos simulados del pago
$pago = [
    'folio'    => 'UICM-2026-0001',
    'nombre'   => 'Juan Pérez García',
    'programa' => 'Ingeniería en Sistemas Computacionales',
    'monto'    => '$3,500 MXN',
    'fecha'    => '15/02/2026',
    'estado'   => 'pago_en_revision',
    'banco'    => 'BBVA',
    'cuenta'   => '0123456789',
];
@endphp

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-4xl">

        {{-- Migas de navegación --}}
        <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
            <a href="{{ route('dashboard') }}" class="hover:text-uicm-green transition-colors">Panel</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('finanzas.pagos.index') }}" class="hover:text-uicm-green transition-colors">Pagos en revisión</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-600">{{ $pago['folio'] }}</span>
        </nav>

        {{-- Encabezado del expediente --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                        Comprobante de pago
                    </p>
                    <h1 class="text-xl font-extrabold text-gray-900">{{ $pago['nombre'] }}</h1>
                    <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $pago['folio'] }}</p>
                </div>

                {{-- Badge estado --}}
                @if ($pago['estado'] === 'pago_en_revision')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white self-start sm:self-auto"
                          style="background-color: #EFAD5A;">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Pago en revisión
                    </span>
                @elseif ($pago['estado'] === 'pago_validado')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white self-start sm:self-auto"
                          style="background-color: #0F4229;">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Pago validado
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-gray-600 bg-gray-200 self-start sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                        Rechazado
                    </span>
                @endif
            </div>
        </div>

        {{-- Dos columnas: datos + comprobante --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">

            {{-- Datos del aspirante (3/5) --}}
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Datos del aspirante</h2>
                </div>

                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Nombre completo</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $pago['nombre'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Folio</dt>
                            <dd class="text-sm font-mono font-bold tracking-widest" style="color: #0F4229;">
                                {{ $pago['folio'] }}
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Programa</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $pago['programa'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Monto declarado</dt>
                            <dd class="text-lg font-extrabold" style="color: #0F4229;">{{ $pago['monto'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Fecha de pago</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $pago['fecha'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Banco</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $pago['banco'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Número de cuenta</dt>
                            <dd class="text-sm font-mono font-semibold text-gray-800">{{ $pago['cuenta'] }}</dd>
                        </div>

                    </dl>
                </div>
            </div>

            {{-- Comprobante (2/5) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586
                                 a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6
                                 a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Comprobante</h2>
                </div>

                <div class="px-6 py-5">
                    {{-- Imagen del comprobante con fallback --}}
                    <div class="relative rounded-xl overflow-hidden border border-gray-200 bg-gray-50">

                        {{-- Imagen real (si existe) --}}
                        <img id="img-comprobante"
                             src="{{ asset('images/comprobante-demo.jpg') }}"
                             alt="Comprobante de pago"
                             class="w-full object-contain max-h-64"
                             onerror="this.style.display='none'; document.getElementById('img-fallback').style.display='flex';">

                        {{-- Fallback visual --}}
                        <div id="img-fallback"
                             class="hidden items-center justify-center flex-col gap-3 py-12 px-4 text-center"
                             style="display: none;">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586
                                         a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6
                                         a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-400 font-medium">Vista previa del comprobante</p>
                            <p class="text-xs text-gray-300">Imagen no disponible</p>
                        </div>

                    </div>

                    {{-- Link de descarga simulado --}}
                    <div class="mt-4">
                        <a href="#"
                           class="inline-flex items-center gap-2 text-xs font-semibold transition-colors duration-150"
                           style="color: #D4AF37;"
                           onmouseover="this.style.textDecoration='underline'"
                           onmouseout="this.style.textDecoration='none'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar comprobante
                        </a>
                    </div>
                </div>
            </div>

        </div>{{-- /grid --}}

        {{-- Acciones principales --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Resolución del pago</h2>
            </div>
            <div class="px-6 py-5 flex flex-col sm:flex-row items-center gap-4">

                {{-- Aprobar pago --}}
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
                    Aprobar pago
                </button>

                {{-- Rechazar pago --}}
                <button type="button"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                               px-6 py-3 rounded-xl text-sm font-bold text-white
                               bg-gray-400 hover:bg-gray-500 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Rechazar pago
                </button>

                {{-- Volver --}}
                <a href="{{ route('finanzas.pagos.index') }}"
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
        </div>

    </div>
</section>

@endsection
