@extends('layouts.app')

@section('title', 'Estadísticas | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-6xl">

        <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Finanzas</p>
                <h1 class="text-2xl font-extrabold text-gray-900">Estadísticas de pagos</h1>
                <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
            </div>
            <a href="{{ route('finanzas.pagos.index') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold border-2"
               style="border-color: #0F4229; color: #0F4229;">
                Volver a pagos
            </a>
        </div>

        {{-- Totales --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total recaudado</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">${{ number_format($totalRecaudado, 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pendiente por cobrar</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">${{ number_format($montoPendiente, 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm px-5 py-4 border-l-4 border-red-500">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Atrasado</p>
                <p class="text-2xl font-extrabold mt-1 text-red-600">${{ number_format($montoAtrasado, 2) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Por concepto --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Recaudado por concepto</h2>
                </div>
                <div class="px-6 py-4">
                    @forelse ($porConcepto as $concepto => $datos)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-700 capitalize">{{ $concepto }}</span>
                            <span class="text-sm text-gray-400">{{ $datos['cantidad'] }} pago(s)</span>
                            <span class="text-sm font-bold" style="color: #0F4229;">${{ number_format($datos['monto'], 2) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-4 text-center">Sin pagos validados todavía.</p>
                    @endforelse
                </div>
            </div>

            {{-- Por mes --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Recaudado por mes</h2>
                </div>
                <div class="px-6 py-4">
                    @forelse ($porMes as $mes => $monto)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-700">{{ $mes }}</span>
                            <span class="text-sm font-bold" style="color: #0F4229;">${{ number_format($monto, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-4 text-center">Sin pagos validados todavía.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</section>
@endsection
