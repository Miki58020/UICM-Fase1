@extends('layouts.app')

@section('title', 'Mi horario | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Alumno</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Mi horario</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5
                             a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700">Horario asignado</h2>
                <span class="ml-auto text-xs text-gray-400 text-right">
                    {{ $cargas->count() }} {{ $cargas->count() === 1 ? 'materia' : 'materias' }}
                    · {{ $alumno->grupo->clave ?? '—' }}
                </span>
            </div>

            <div class="overflow-x-auto">
                @if ($cargas->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">
                        No hay horario asignado. El grupo aún no tiene carga académica registrada.
                    </div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Materia</th>
                            <th class="px-6 py-3">Profesor</th>
                            <th class="px-6 py-3">Horario</th>
                            <th class="px-6 py-3">Aula virtual</th>
                            <th class="px-6 py-3">Período</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($cargas as $c)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">
                            <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                {{ $c->materia->nombre ?? '—' }}
                                <span class="block text-xs text-gray-400 font-normal">{{ $c->materia->clave ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                {{ $c->profesor->nombre ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $c->horario ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.75 10.5l4.72-2.72a.75.75 0 011.03.6v7.24a.75.75 0 01-1.03.6L15.75 14
                                                 M4.5 6.75h9a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5h-9a1.5 1.5 0 01-1.5-1.5v-7.5a1.5 1.5 0 011.5-1.5z"/>
                                    </svg>
                                    {{ $c->aula ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs whitespace-nowrap">
                                {{ $c->periodo->nombre ?? '—' }}
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
