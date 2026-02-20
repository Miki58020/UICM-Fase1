@extends('layouts.app')

@section('title', 'Estado de Inscripción | UICM')

@section('content')

{{--
    Vista simulada — datos estáticos.
    Al conectar el backend, reemplazar con variables del controlador:
    $nombre, $programa, $folio, $estadoActual, $pasos
--}}

@php
    $nombre   = 'Juan Pérez';
    $programa = 'Psicopedagogía';
    $folio    = request('folio', 'UICM-2026-0001');

    // Índice 0-4 del paso actual (0=enviada … 4=completada)
    $pasoActual = 3; // "Pago en revisión"

    $pasos = [
        'Solicitud enviada',
        'Datos validados',
        'Pago pendiente',
        'Pago en revisión',
        'Inscripción completada',
    ];
@endphp

<section class="bg-uicm-gray min-h-screen py-12">
    <div class="container mx-auto px-8 lg:px-24">

        {{-- Encabezado --}}
        <div class="text-center mb-8">
            <p class="text-uicm-gold text-xs font-bold uppercase tracking-widest mb-2">Admisiones</p>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Estado de inscripción</h1>
            <div class="mx-auto w-16 h-1 rounded-full" style="background-color: #D4AF37;"></div>
        </div>

        <div class="max-w-3xl mx-auto space-y-6">

            {{-- ══════════════════════════════════
                 CARD: Datos del aspirante
            ══════════════════════════════════ --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-2 w-full" style="background-color: #0F4229;"></div>

                <div class="p-6 md:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-0.5">Aspirante</p>
                            <h2 class="text-xl font-extrabold text-gray-900">{{ $nombre }}</h2>
                        </div>
                        {{-- Badge estado --}}
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white self-start sm:self-auto"
                              style="background-color: #EFAD5A;">
                            <span class="w-2 h-2 rounded-full bg-white opacity-80"></span>
                            Pago en revisión
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="bg-uicm-gray rounded-xl p-4">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Folio</p>
                            <p class="font-bold text-uicm-green tracking-widest font-mono">{{ $folio }}</p>
                        </div>
                        <div class="bg-uicm-gray rounded-xl p-4">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Programa</p>
                            <p class="font-semibold text-gray-800">{{ $programa }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════
                 CARD: Línea de progreso
            ══════════════════════════════════ --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="p-6 md:p-8">
                    <h3 class="text-base font-bold text-gray-800 mb-6">Progreso del proceso</h3>

                    {{-- Stepper — horizontal en md+, vertical en móvil --}}
                    {{-- VERSIÓN ESCRITORIO --}}
                    <div class="hidden md:flex items-start justify-between relative">

                        {{-- Línea de fondo conectora --}}
                        <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-200 z-0" style="margin: 0 2rem;"></div>

                        @foreach($pasos as $i => $paso)
                        <div class="flex flex-col items-center flex-1 relative z-10">
                            {{-- Círculo --}}
                            @if($i < $pasoActual)
                                {{-- Completado --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm mb-3"
                                     style="background-color: #0F4229;">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            @elseif($i === $pasoActual)
                                {{-- Actual --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm mb-3 ring-4 ring-orange-200"
                                     style="background-color: #EFAD5A;">
                                    <div class="w-2.5 h-2.5 rounded-full bg-white"></div>
                                </div>
                            @else
                                {{-- Pendiente --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mb-3 bg-gray-100 border-2 border-gray-200">
                                    <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                </div>
                            @endif

                            {{-- Etiqueta --}}
                            <p class="text-center text-xs font-semibold leading-tight px-1
                                {{ $i < $pasoActual ? 'text-uicm-green' : ($i === $pasoActual ? 'text-orange-500' : 'text-gray-400') }}">
                                {{ $paso }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    {{-- VERSIÓN MÓVIL --}}
                    <div class="flex flex-col gap-0 md:hidden">
                        @foreach($pasos as $i => $paso)
                        <div class="flex gap-4 items-start">
                            {{-- Columna de ícono + línea --}}
                            <div class="flex flex-col items-center flex-shrink-0">
                                @if($i < $pasoActual)
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm"
                                         style="background-color: #0F4229;">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @elseif($i === $pasoActual)
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm ring-4 ring-orange-200"
                                         style="background-color: #EFAD5A;">
                                        <div class="w-2.5 h-2.5 rounded-full bg-white"></div>
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 border-2 border-gray-200">
                                        <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                    </div>
                                @endif
                                {{-- Conector vertical --}}
                                @if(!$loop->last)
                                <div class="w-0.5 flex-1 my-1"
                                     style="height: 28px; background-color: {{ $i < $pasoActual ? '#0F4229' : '#E5E7EB' }};"></div>
                                @endif
                            </div>

                            {{-- Texto --}}
                            <p class="pt-1.5 pb-6 text-sm font-semibold
                                {{ $i < $pasoActual ? 'text-uicm-green' : ($i === $pasoActual ? 'text-orange-500' : 'text-gray-400') }}">
                                {{ $paso }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════
                 NOTA INFORMATIVA
            ══════════════════════════════════ --}}
            <div class="rounded-xl p-5 border-l-4 bg-yellow-50"
                 style="border-color: #D4AF37;">
                <div class="flex gap-3 items-start">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="#D4AF37" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        Tu comprobante de pago está siendo revisado por el área de finanzas.
                        El proceso de validación puede tardar hasta <strong>2 días hábiles</strong>.
                        Te notificaremos por correo cuando haya una actualización.
                    </p>
                </div>
            </div>

            {{-- ══════════════════════════════════
                 BOTONES
            ══════════════════════════════════ --}}

            {{-- Botón de pago — visible solo cuando el estado es "Pago pendiente" (índice 2) --}}
            @if($pasoActual === 2)
            <a href="{{ route('aspirantes.pago') }}"
               class="block w-full py-3.5 rounded-xl text-center text-white text-sm font-bold
                      transition-colors duration-200 shadow-md"
               style="background-color: #EFAD5A;"
               onmouseover="this.style.backgroundColor='#e09a3a'"
               onmouseout="this.style.backgroundColor='#EFAD5A'">
                Realizar pago
            </a>
            @endif

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('aspirantes.seguimiento') }}"
                   class="flex-1 py-3 rounded-xl border-2 font-semibold text-sm text-center transition-colors duration-200"
                   style="border-color: #0F4229; color: #0F4229;"
                   onmouseover="this.style.backgroundColor='#f0f9f4'"
                   onmouseout="this.style.backgroundColor='transparent'">
                    Consultar otro folio
                </a>
                <a href="{{ route('home') }}"
                   class="flex-1 py-3 rounded-xl text-white font-semibold text-sm text-center transition-colors duration-200 shadow-md"
                   style="background-color: #0F4229;"
                   onmouseover="this.style.backgroundColor='#0a2f1c'"
                   onmouseout="this.style.backgroundColor='#0F4229'">
                    Volver al inicio
                </a>
            </div>

        </div>
    </div>
</section>

@endsection
