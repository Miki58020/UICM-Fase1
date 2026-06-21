@extends('layouts.app')

@section('title', 'Pagar | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 max-w-2xl">

        <div class="text-center mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: #D4AF37;">Portal del Alumno</p>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Pagar {{ ucfirst($pago->concepto) }}</h1>
            <div class="mx-auto w-16 h-1 rounded-full" style="background-color: #D4AF37;"></div>
        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-uicm-gray rounded-xl p-4">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Concepto</p>
                            <p class="font-semibold text-sm text-gray-800 capitalize">
                                {{ $pago->concepto }}{{ $pago->mes ? ' '.$pago->mes : '' }}
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
                    <h3 class="text-sm font-semibold text-gray-700">Pago seguro con Mercado Pago</h3>
                </div>
                <div class="px-6 py-8 flex flex-col items-center gap-5 text-center">
                    <p class="text-sm text-gray-500 max-w-sm">
                        Al dar clic serás redirigido al sitio seguro de Mercado Pago para completar
                        tu pago de <span class="font-bold text-gray-800">${{ number_format($pago->monto, 0) }} MXN</span>.
                        Al terminar regresarás automáticamente al portal.
                    </p>

                    @if ($checkoutUrl)
                        <a href="{{ $checkoutUrl }}"
                           class="inline-flex items-center gap-3 px-8 py-3.5 rounded-xl text-white font-bold text-sm shadow-md transition-all duration-150 hover:opacity-90 active:scale-95"
                           style="background-color: #009ee3;">
                            Pagar con Mercado Pago
                        </a>
                    @else
                        <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-3 w-full max-w-sm">
                            No se pudo generar el enlace de pago. Por favor, recarga la página.
                        </p>
                    @endif
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
