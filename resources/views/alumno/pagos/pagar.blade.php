@extends('layouts.app')

@section('title', 'Pagar | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 max-w-2xl">

        @php
            $labelsConcepto = [
                'inscripcion'  => 'Inscripción',
                'colegiatura'  => 'Colegiatura',
                'cuatrimestre' => 'Reinscripción',
                'otro'         => 'Otro',
            ];
            $conceptoLabel = $labelsConcepto[$pago->concepto] ?? ucfirst($pago->concepto);
        @endphp

        <div class="text-center mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: #D4AF37;">Portal del Alumno</p>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Pagar {{ $conceptoLabel }}</h1>
            <div class="mx-auto w-16 h-1 rounded-full" style="background-color: #D4AF37;"></div>
        </div>

        <div class="space-y-6">

            @php
                $pagoAtrasado = method_exists($pago, 'estaVencido') && $pago->estaVencido();
            @endphp
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                <div class="px-6 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Alumno</p>
                            <h2 class="text-lg font-extrabold text-gray-900">{{ $pago->alumno->nombre_completo }}</h2>
                        </div>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white self-start sm:self-auto"
                              style="background-color: {{ $pagoAtrasado ? '#dc2626' : '#EFAD5A' }};">
                            <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                            {{ $pagoAtrasado ? 'Pago atrasado' : 'Pago pendiente' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-5">
                        <div class="bg-uicm-gray rounded-xl p-4">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Concepto</p>
                            <p class="font-semibold text-sm text-gray-800">
                                {{ $conceptoLabel }}{{ $pago->mes ? ' '.$pago->mes : '' }}
                            </p>
                        </div>
                        <div class="bg-uicm-gray rounded-xl p-4">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Vencimiento</p>
                            <p class="font-semibold text-sm" style="color: {{ $pagoAtrasado ? '#dc2626' : '#1f2937' }};">
                                {{ $pago->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                            </p>
                        </div>
                        <div class="bg-uicm-gray rounded-xl p-4">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Monto</p>
                            @if ($pago->descuento > 0)
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-sm text-gray-400 line-through">${{ number_format($pago->monto_original, 0) }}</span>
                                    <span class="text-xs font-bold px-1.5 py-0.5 rounded-lg" style="background-color:#fef4e8; color:#b45309;">
                                        {{ number_format($pago->descuento, 0) }}% dto.
                                    </span>
                                </div>
                            @endif
                            <p class="text-xl font-extrabold" style="color: #0F4229;">${{ number_format($pago->monto, 0) }} MXN</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #D4AF37;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700">Pago seguro con Mercado Pago</h3>
                </div>
                <div class="px-6 py-8 flex flex-col items-center gap-5 text-center">
                    <p class="text-sm text-gray-500 max-w-sm">
                        Al dar clic serás redirigido al sitio seguro de Mercado Pago para completar
                        tu pago de <span class="font-bold text-gray-800">${{ number_format($pago->monto, 0) }} MXN</span>.
                        Al terminar regresarás automáticamente al portal.
                        @if ($pago->descuento > 0)
                            <span class="block mt-1 text-xs" style="color:#b45309;">
                                Incluye {{ number_format($pago->descuento, 0) }}% de descuento sobre el precio base de ${{ number_format($pago->monto_original, 0) }} MXN.
                            </span>
                        @endif
                    </p>

                    @if ($checkoutUrl)
                        <a href="{{ $checkoutUrl }}"
                           class="inline-flex items-center gap-3 px-8 py-3.5 rounded-xl text-white font-bold text-sm shadow-md transition-all duration-150 hover:opacity-90 active:scale-95"
                           style="background-color: #009ee3;">
                            <svg class="w-5 h-5" viewBox="0 0 40 26" fill="none">
                                <path d="M20 0C9 0 0 5.82 0 13s9 13 20 13 20-5.82 20-13S31 0 20 0z" fill="white" fill-opacity="0.25"/>
                                <path d="M11 13a9 9 0 0118 0" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                                <circle cx="20" cy="13" r="2.5" fill="white"/>
                            </svg>
                            Pagar con Mercado Pago
                        </a>
                    @else
                        <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-3 w-full max-w-sm">
                            No se pudo generar el enlace de pago. Por favor, recarga la página.
                        </p>
                    @endif

                    <p class="text-xs text-gray-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Pago procesado de forma segura por Mercado Pago
                    </p>
                </div>
            </div>

            <div class="flex justify-start">
                <a href="{{ route('alumno.finanzas.index') }}"
                   class="group inline-flex items-center gap-3 px-4 py-3 rounded-xl bg-white shadow-sm border border-gray-200 hover:border-gray-300 hover:shadow transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="text-xs font-semibold text-gray-500 group-hover:text-gray-700">Regresar</span>
                </a>
            </div>

        </div>
    </div>
</section>
@endsection
