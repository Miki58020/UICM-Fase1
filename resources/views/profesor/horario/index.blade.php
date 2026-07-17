@extends('layouts.app')

@section('title', 'Horario | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Profesor</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Horario</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        @if ($cargas->isEmpty())
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5
                                     a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin horario asignado</h3>
                    <p class="text-sm text-gray-500 max-w-xs">Aún no tienes grupos ni materias asignadas.</p>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Período</th>
                                <th class="px-6 py-3">Grupo</th>
                                <th class="px-6 py-3">Materia</th>
                                <th class="px-6 py-3">Horario</th>
                                <th class="px-6 py-3">Aula virtual</th>
                                <th class="px-6 py-3">Fechas de captura</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($cargas as $carga)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-600 text-xs">
                                    {{ $carga->periodo->nombre ?? '—' }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-700">
                                    {{ $carga->grupo->clave ?? '—' }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $carga->materia->nombre ?? '—' }}
                                    <span class="block text-xs text-gray-400">{{ $carga->materia->clave ?? '' }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-xs">
                                    {{ $carga->horario_formateado ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-xs">
                                    {{ $carga->aula ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                                    @if ($carga->fecha_inicio && $carga->fecha_fin)
                                        {{ $carga->fecha_inicio->format('d/m/Y') }} - {{ $carga->fecha_fin->format('d/m/Y') }}
                                    @else
                                        <span class="text-orange-500 font-medium">Sin definir</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</section>
@endsection
