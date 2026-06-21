@extends('layouts.app')

@section('title', 'Alumnos al corriente | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-6xl">

        <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Finanzas</p>
                <h1 class="text-2xl font-extrabold text-gray-900">Alumnos: al corriente / atrasados</h1>
                <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
            </div>
            <a href="{{ route('finanzas.pagos.index') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold border-2"
               style="border-color: #0F4229; color: #0F4229;">
                Volver a pagos
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Al corriente</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $alCorriente->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 border-red-500">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Atrasados</p>
                <p class="text-2xl font-extrabold mt-1 text-red-600">{{ $atrasados->count() }}</p>
            </div>
        </div>

        {{-- Atrasados --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full bg-red-500"></div>
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Alumnos atrasados</h2>
            </div>
            <div class="overflow-x-auto">
                @if ($atrasados->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">No hay alumnos atrasados.</div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Matrícula</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3 text-center">Pagos atrasados</th>
                            <th class="px-6 py-3 text-right">Monto atrasado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($atrasados as $alumno)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono text-xs text-gray-600 whitespace-nowrap">{{ $alumno->matricula }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">{{ $alumno->nombre_completo }}</td>
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $alumno->programa?->nombre ?? '—' }}</td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ $alumno->cantidadAtrasados }}</td>
                            <td class="px-6 py-4 text-right font-bold text-red-600 whitespace-nowrap">${{ number_format($alumno->montoAtrasado, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        {{-- Al corriente --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Alumnos al corriente</h2>
            </div>
            <div class="overflow-x-auto">
                @if ($alCorriente->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">No hay alumnos registrados.</div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Matrícula</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Programa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($alCorriente as $alumno)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono text-xs text-gray-600 whitespace-nowrap">{{ $alumno->matricula }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">{{ $alumno->nombre_completo }}</td>
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $alumno->programa?->nombre ?? '—' }}</td>
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
