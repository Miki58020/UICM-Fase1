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
            {{-- Tarjetas KPI --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="h-1.5 w-full flex-shrink-0" style="background-color: #0F4229;"></div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 mb-4"
                             style="background-color: #e6f2ec;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-4xl font-extrabold leading-none mb-1" style="color: #0F4229;">{{ $grupos->count() }}</p>
                        <p class="text-xs font-medium text-gray-500">Grupos asignados</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="h-1.5 w-full flex-shrink-0" style="background-color: #D4AF37;"></div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 mb-4"
                             style="background-color: #fbf3de;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #D4AF37;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                                         C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                                         C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                                         C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <p class="text-4xl font-extrabold leading-none mb-1" style="color: #D4AF37;">{{ $totalMaterias }}</p>
                        <p class="text-xs font-medium text-gray-500">Materias a tu cargo</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="h-1.5 w-full flex-shrink-0" style="background-color: #EFAD5A;"></div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 mb-4"
                             style="background-color: #fdf0e0;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #EFAD5A;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <p class="text-4xl font-extrabold leading-none mb-1" style="color: #EFAD5A;">{{ $totalAlumnosActivos }}</p>
                        <p class="text-xs font-medium text-gray-500">Alumnos activos</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @foreach ($grupos as $g)
                <a href="{{ route('profesor.alumnos.index', ['grupo_id' => $g->grupo->id]) }}"
                   class="block bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">

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
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green">
                                {{ $g->materias->count() }} {{ $g->materias->count() === 1 ? 'materia' : 'materias' }}
                            </span>
                            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>

                    <div class="px-6 py-5">
                        <div class="flex items-center gap-1.5 mb-3">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block" style="background-color: #EFAD5A;"></span>
                            <span class="text-xs font-medium text-gray-500">
                                {{ $g->alumnosActivos }} {{ $g->alumnosActivos === 1 ? 'alumno activo' : 'alumnos activos' }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($g->materias as $materia)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-uicm-gold-soft text-uicm-gold-soft-text">
                                    {{ $materia->nombre }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        @endif

    </div>
</section>
@endsection
