@extends('layouts.app')

@section('title', 'Mis finanzas | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        <div class="mb-8">
            <a href="{{ route('alumno.dashboard') }}"
               class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4 w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Mi portal
            </a>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Alumno</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Mis finanzas</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Estado general --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: {{ $alCorriente ? '#0F4229' : '#dc2626' }};"></div>
            <div class="px-6 py-5 flex items-center gap-3">
                @if ($alCorriente)
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white" style="background-color: #0F4229;">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Al corriente
                    </span>
                    <p class="text-sm text-gray-500">No tienes pagos atrasados.</p>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white bg-red-600">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Atrasado
                    </span>
                    <p class="text-sm text-gray-500">
                        Tienes {{ $atrasados->count() }} pago(s) vencido(s). Tu acceso al portal no se ve afectado, pero te recomendamos ponerte al corriente.
                    </p>
                @endif
            </div>
        </div>

        {{-- Recordatorios --}}
        @if ($porVencerPronto->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            @foreach ($porVencerPronto as $pago)
            <div class="bg-white rounded-2xl shadow-sm border border-amber-200 px-5 py-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: #fef4e8;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #D4AF37;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">{{ ucfirst($pago->concepto) }}{{ $pago->mes ? ' '.$pago->mes : '' }}</p>
                    <p class="text-sm font-bold text-gray-800">Vence el {{ $pago->fecha_vencimiento->format('d/m/Y') }}</p>
                </div>
                <a href="{{ route('alumno.pagos.pagar', $pago) }}"
                   class="text-xs font-bold px-4 py-2 rounded-lg text-white whitespace-nowrap" style="background-color: #0F4229;">
                    Pagar
                </a>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Pagos pendientes --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <h2 class="text-sm font-semibold text-gray-700">Pagos pendientes</h2>
                <span class="ml-auto text-xs text-gray-400">{{ $pendientes->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                @if ($pendientes->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">No tienes pagos pendientes.</div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Concepto</th>
                            <th class="px-6 py-3">Periodo</th>
                            <th class="px-6 py-3 text-right">Monto</th>
                            <th class="px-6 py-3">Vencimiento</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                            <th class="px-6 py-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($pendientes as $pago)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800 capitalize whitespace-nowrap">
                                {{ $pago->concepto }}{{ $pago->mes ? ' '.$pago->mes : '' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $pago->periodo }}</td>
                            <td class="px-6 py-4 text-right font-bold whitespace-nowrap" style="color: #0F4229;">
                                ${{ number_format($pago->monto, 2) }} MXN
                                @if ($pago->descuento > 0)
                                    <span class="block text-xs font-normal text-gray-400">{{ number_format($pago->descuento, 0) }}% dto. aplicado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                {{ $pago->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if ($pago->estaVencido())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #dc2626;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Atrasado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #EFAD5A;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('alumno.pagos.pagar', $pago) }}"
                                   class="text-xs font-bold px-4 py-2 rounded-lg text-white" style="background-color: #0F4229;">
                                    Pagar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        {{-- Historial --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <h2 class="text-sm font-semibold text-gray-700">Historial de pagos</h2>
                <span class="ml-auto text-xs text-gray-400">{{ $historial->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                @if ($historial->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">Aún no hay pagos en tu historial.</div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Concepto</th>
                            <th class="px-6 py-3">Periodo</th>
                            <th class="px-6 py-3 text-right">Monto</th>
                            <th class="px-6 py-3">Fecha de pago</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                            <th class="px-6 py-3 text-right">Comprobante</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($historial as $pago)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800 capitalize whitespace-nowrap">
                                {{ $pago->concepto }}{{ $pago->mes ? ' '.$pago->mes : '' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $pago->periodo }}</td>
                            <td class="px-6 py-4 text-right font-bold whitespace-nowrap" style="color: #0F4229;">
                                ${{ number_format($pago->monto, 2) }} MXN
                            </td>
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                {{ $pago->fecha_pago?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if ($pago->estado === 'aprobado')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Pagado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #dc2626;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Rechazado
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if ($pago->estado === 'aprobado')
                                    <a href="{{ route('alumno.comprobante', $pago) }}" target="_blank"
                                       class="text-xs font-semibold" style="color: #0F4229;">Ver comprobante</a>
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
</section>
@endsection
