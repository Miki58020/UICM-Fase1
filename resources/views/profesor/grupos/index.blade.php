@extends('layouts.app')

@section('title', 'Grupos | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Profesor</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Grupos</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        @if ($grupos->isEmpty())
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                                     M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                                     m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin grupos asignados</h3>
                    <p class="text-sm text-gray-500 max-w-xs">Aún no tienes grupos ni materias asignadas.</p>
                </div>
            </div>
        @else
            {{-- Tarjetas resumen --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Grupos asignados</p>
                    <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $grupos->count() }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Materias a tu cargo</p>
                    <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">{{ $totalMaterias }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Alumnos activos</p>
                    <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $totalAlumnosActivos }}</p>
                </div>
            </div>

            <div class="space-y-6">
                @foreach ($grupos as $g)
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                    <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: #e6f2ec;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-800">
                                    {{ $g->grupo->clave ?? '—' }}
                                    <span class="font-normal text-gray-400">— {{ $g->grupo->programa->nombre ?? '—' }}</span>
                                </h2>
                                <p class="text-xs text-gray-400">{{ $g->grupo->cuatrimestre ?? '—' }}° cuatrimestre · {{ $g->periodo->nombre ?? '—' }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">
                            {{ $g->materias->count() }} {{ $g->materias->count() === 1 ? 'materia' : 'materias' }}
                            · {{ $g->alumnosActivos }} {{ $g->alumnosActivos === 1 ? 'alumno activo' : 'alumnos activos' }}
                        </span>
                    </div>

                    <div class="px-6 py-5">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($g->materias as $materia)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold text-white"
                                      style="background-color: #D4AF37;">
                                    {{ $materia->nombre }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </div>
</section>
@endsection
