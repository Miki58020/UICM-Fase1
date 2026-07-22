@extends('layouts.app')

@section('title', 'Alumnos | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Profesor</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Alumnos</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        @if ($alumnos->isEmpty())
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin alumnos asignados</h3>
                    <p class="text-sm text-gray-500 max-w-xs">Aún no tienes grupos ni materias asignadas.</p>
                </div>
            </div>
        @else
            {{-- Tarjetas resumen --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Alumnos activos</p>
                    <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $alumnos->count() }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Grupos</p>
                    <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $totalGrupos }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Alumno</th>
                                <th class="px-6 py-3">Grupo</th>
                                <th class="px-6 py-3">Materias que le impartes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($alumnos as $a)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                             style="background-color: #0F4229;">
                                            {{ strtoupper(substr($a->alumno->nombre, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 whitespace-nowrap">{{ $a->alumno->nombre_completo }}</p>
                                            <p class="text-xs text-gray-400 whitespace-nowrap">{{ $a->alumno->matricula }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-700 whitespace-nowrap">
                                    {{ $a->grupo->clave ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($a->materias as $materia)
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium text-gray-600 bg-uicm-gray whitespace-nowrap">
                                                {{ $materia->nombre }}
                                            </span>
                                        @endforeach
                                    </div>
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
