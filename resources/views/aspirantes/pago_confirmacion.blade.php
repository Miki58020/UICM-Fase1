@extends('layouts.app')

@section('title', 'Estado del Pago | UICM')

@section('content')

<section class="bg-uicm-gray min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        @if($status === 'aprobado')
        {{-- ══════ PAGO APROBADO ══════ --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-8 py-10 text-center">
                <div class="flex items-center justify-center w-16 h-16 rounded-full mx-auto mb-5"
                     style="background-color: #f0f9f4;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h1 class="text-xl font-extrabold text-gray-900 mb-2">¡Pago confirmado!</h1>
                <div class="w-12 h-0.5 mx-auto my-4" style="background-color: #D4AF37;"></div>

                <div class="flex justify-center mb-5">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white"
                          style="background-color: #0F4229;">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Pago aprobado
                    </span>
                </div>

                <p class="text-sm text-gray-600 leading-relaxed mb-8">
                    Tu pago fue procesado correctamente. Recibirás un correo de confirmación.
                    El área de control escolar completará tu inscripción en breve.
                </p>

                <a href="{{ route('home') }}"
                   class="block w-full py-3 rounded-xl text-white text-sm font-bold text-center transition-colors duration-200 shadow-sm mb-3"
                   style="background-color: #0F4229;"
                   onmouseover="this.style.backgroundColor='#0a2e1c'"
                   onmouseout="this.style.backgroundColor='#0F4229'">
                    Volver al inicio
                </a>
                <a href="{{ route('aspirantes.seguimiento') }}"
                   class="block text-sm font-medium transition-colors duration-150"
                   style="color: #0F4229;"
                   onmouseover="this.style.textDecoration='underline'"
                   onmouseout="this.style.textDecoration='none'">
                    Consultar estado de inscripción
                </a>
            </div>
        </div>

        @elseif($status === 'pendiente')
        {{-- ══════ PAGO PENDIENTE (ej. OXXO) ══════ --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #EFAD5A;"></div>

            <div class="px-8 py-10 text-center">
                <div class="flex items-center justify-center w-16 h-16 rounded-full mx-auto mb-5"
                     style="background-color: #fffbeb;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #EFAD5A;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <h1 class="text-xl font-extrabold text-gray-900 mb-2">Pago en proceso</h1>
                <div class="w-12 h-0.5 mx-auto my-4" style="background-color: #D4AF37;"></div>

                <div class="flex justify-center mb-5">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white"
                          style="background-color: #EFAD5A;">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Pago pendiente
                    </span>
                </div>

                <p class="text-sm text-gray-600 leading-relaxed mb-6">
                    Tu pago está siendo procesado. Si elegiste pagar en efectivo (OXXO u otro punto),
                    realiza el pago con el ticket que te generó Mercado Pago.
                </p>

                <div class="rounded-xl p-4 bg-yellow-50 border-l-4 text-left mb-8"
                     style="border-color: #D4AF37;">
                    <div class="flex gap-3 items-start">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="#D4AF37" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Una vez acreditado el pago recibirás un correo de confirmación.
                            Este proceso puede tardar hasta <strong>48 horas</strong>.
                        </p>
                    </div>
                </div>

                <a href="{{ route('home') }}"
                   class="block w-full py-3 rounded-xl text-white text-sm font-bold text-center transition-colors duration-200 shadow-sm mb-3"
                   style="background-color: #0F4229;"
                   onmouseover="this.style.backgroundColor='#0a2e1c'"
                   onmouseout="this.style.backgroundColor='#0F4229'">
                    Volver al inicio
                </a>
                <a href="{{ route('aspirantes.seguimiento') }}"
                   class="block text-sm font-medium transition-colors duration-150"
                   style="color: #0F4229;"
                   onmouseover="this.style.textDecoration='underline'"
                   onmouseout="this.style.textDecoration='none'">
                    Consultar estado de inscripción
                </a>
            </div>
        </div>

        @else
        {{-- ══════ PAGO RECHAZADO / ERROR ══════ --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="h-1.5 w-full bg-red-500"></div>

            <div class="px-8 py-10 text-center">
                <div class="flex items-center justify-center w-16 h-16 rounded-full mx-auto mb-5 bg-red-50">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>

                <h1 class="text-xl font-extrabold text-gray-900 mb-2">Pago no completado</h1>
                <div class="w-12 h-0.5 mx-auto my-4 bg-red-300"></div>

                <div class="flex justify-center mb-5">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white bg-red-500">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Pago rechazado
                    </span>
                </div>

                <p class="text-sm text-gray-600 leading-relaxed mb-8">
                    Tu pago no pudo procesarse. Verifica los datos de tu tarjeta o elige otro método de pago e intenta de nuevo.
                </p>

                <a href="{{ route('aspirantes.seguimiento') }}"
                   class="block w-full py-3 rounded-xl text-white text-sm font-bold text-center transition-colors duration-200 shadow-sm mb-3"
                   style="background-color: #0F4229;"
                   onmouseover="this.style.backgroundColor='#0a2e1c'"
                   onmouseout="this.style.backgroundColor='#0F4229'">
                    Intentar de nuevo
                </a>
                <a href="{{ route('home') }}"
                   class="block text-sm font-medium transition-colors duration-150 text-gray-400"
                   onmouseover="this.style.textDecoration='underline'"
                   onmouseout="this.style.textDecoration='none'">
                    Volver al inicio
                </a>
            </div>
        </div>
        @endif

    </div>
</section>

@endsection
