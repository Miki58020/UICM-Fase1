@extends('layouts.app')

@section('title', 'Comprobante Enviado | UICM')

@section('content')

<section class="bg-uicm-gray min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        {{-- Card principal --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-8 py-10 text-center">

                {{-- Ícono de éxito --}}
                <div class="flex items-center justify-center w-16 h-16 rounded-full mx-auto mb-5"
                     style="background-color: #f0f9f4;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                {{-- Título --}}
                <h1 class="text-xl font-extrabold text-gray-900 mb-2">
                    Comprobante enviado correctamente
                </h1>

                {{-- Línea decorativa --}}
                <div class="w-12 h-0.5 mx-auto my-4" style="background-color: #D4AF37;"></div>

                {{-- Badge de estado --}}
                <div class="flex justify-center mb-5">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white"
                          style="background-color: #EFAD5A;">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Pago en revisión
                    </span>
                </div>

                {{-- Texto informativo --}}
                <p class="text-sm text-gray-600 leading-relaxed mb-8">
                    El área de finanzas validará tu pago en un plazo de
                    <strong class="text-gray-800">24 a 48 horas</strong>.
                    Recibirás una notificación cuando tu pago sea confirmado.
                </p>

                {{-- Nota adicional --}}
                <div class="rounded-xl p-4 bg-yellow-50 border-l-4 text-left mb-8"
                     style="border-color: #D4AF37;">
                    <div class="flex gap-3 items-start">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="#D4AF37" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Si tienes alguna duda sobre tu pago, comunícate con el área
                            de finanzas de la institución.
                        </p>
                    </div>
                </div>

                {{-- Botón Volver al inicio --}}
                <a href="{{ route('home') }}"
                   class="block w-full py-3 rounded-xl text-white text-sm font-bold
                          text-center transition-colors duration-200 shadow-sm mb-3"
                   style="background-color: #0F4229;"
                   onmouseover="this.style.backgroundColor='#0a2e1c'"
                   onmouseout="this.style.backgroundColor='#0F4229'">
                    Volver al inicio
                </a>

                {{-- Link consultar estado --}}
                <a href="{{ route('aspirantes.seguimiento') }}"
                   class="block text-sm font-medium transition-colors duration-150"
                   style="color: #0F4229;"
                   onmouseover="this.style.textDecoration='underline'"
                   onmouseout="this.style.textDecoration='none'">
                    Consultar estado de inscripción
                </a>

            </div>
        </div>

    </div>
</section>

@endsection