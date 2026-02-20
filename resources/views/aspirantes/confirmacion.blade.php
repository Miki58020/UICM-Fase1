@extends('layouts.app')

@section('title', 'Solicitud Registrada | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen flex items-center py-16">
    <div class="container mx-auto px-8 lg:px-24">

        <div class="max-w-xl mx-auto">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                {{-- Banner superior verde --}}
                <div class="h-2 w-full" style="background-color: #0F4229;"></div>

                <div class="p-8 md:p-12 text-center">

                    {{-- Ícono de éxito --}}
                    <div class="mx-auto mb-6 w-20 h-20 rounded-full flex items-center justify-center"
                         style="background-color: #e8f5ee;">
                        <svg class="w-10 h-10" fill="none" stroke="#0F4229" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    {{-- Título --}}
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-2">
                        ¡Solicitud registrada!
                    </h1>
                    <div class="mx-auto w-16 h-1 rounded-full mb-6" style="background-color: #D4AF37;"></div>

                    <p class="text-gray-500 text-sm leading-relaxed mb-8">
                        Tu solicitud de ingreso a la <strong class="text-gray-700">Universidad Internacional Cuba México</strong>
                        ha sido recibida correctamente. Pronto recibirás un correo con los siguientes pasos.
                    </p>

                    {{-- Datos simulados --}}
                    <div class="rounded-xl border border-gray-100 bg-uicm-gray p-5 mb-8 text-left space-y-3">

                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Folio</span>
                            <span class="text-sm font-bold text-uicm-green tracking-widest">UICM-2026-0001</span>
                        </div>

                        <hr class="border-gray-200">

                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                                  style="background-color: #FFF8E1; color: #8B6A00;">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: #D4AF37;"></span>
                                Pendiente de validación
                            </span>
                        </div>

                        <hr class="border-gray-200">

                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Fecha de registro</span>
                            <span class="text-sm text-gray-700">{{ now()->format('d/m/Y') }}</span>
                        </div>

                    </div>

                    {{-- Nota --}}
                    <p class="text-xs text-gray-400 leading-relaxed mb-8">
                        Conserva tu número de folio. Lo necesitarás para dar seguimiento
                        a tu proceso de admisión en la plataforma institucional.
                    </p>

                    {{-- Botón volver --}}
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center justify-center gap-2 w-full py-3.5 rounded-xl text-white font-bold text-base transition-colors duration-200 shadow-md"
                       style="background-color: #EFAD5A;"
                       onmouseover="this.style.backgroundColor='#d9973e'"
                       onmouseout="this.style.backgroundColor='#EFAD5A'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 12l6-6M3 12l6 6"/>
                        </svg>
                        Volver al inicio
                    </a>

                </div>
            </div>
        </div>

    </div>
</section>

@endsection
