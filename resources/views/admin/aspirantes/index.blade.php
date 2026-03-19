@extends('layouts.app')

@section('title', 'Validación de Aspirantes | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- ── Encabezado de módulo ── --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Control Escolar
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Validación de Aspirantes</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- ── Flash de éxito ── --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-green-50" style="border-color: #0F4229;">
                <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        {{-- ── Contadores rápidos ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pendientes</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">
                    {{ $aspirantes->where('estado', 'pendiente')->count() }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Aprobados</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">
                    {{ $aspirantes->where('estado', 'aprobado')->count() }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 col-span-2 sm:col-span-1" style="border-color: #9ca3af;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Rechazados</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">
                    {{ $aspirantes->where('estado', 'rechazado')->count() }}
                </p>
            </div>

        </div>

        {{-- ── Card tabla ── --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            {{-- Barra superior verde --}}
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            {{-- Cabecera card --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">
                    Lista de aspirantes
                </h2>
                <span class="text-xs text-gray-400">
                    {{ $aspirantes->count() }} registros
                </span>
            </div>

            {{-- Tabla con scroll horizontal en móvil --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Folio</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($aspirantes as $asp)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">

                            {{-- Folio --}}
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-600 whitespace-nowrap">
                                {{ $asp->folio }}
                            </td>

                            {{-- Nombre --}}
                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                                {{ $asp->nombre_completo }}
                            </td>

                            {{-- Programa --}}
                            <td class="px-6 py-4 text-gray-600">
                                {{ $asp->programa->nombre ?? '—' }}
                            </td>

                            {{-- Badge de estado --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($asp->estado === 'pendiente')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #EFAD5A;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Pendiente
                                    </span>
                                @elseif ($asp->estado === 'aprobado')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Aprobado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                        Rechazado
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.aspirantes.show', $asp->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150 whitespace-nowrap"
                                   style="background-color: #D4AF37;"
                                   onmouseover="this.style.backgroundColor='#b8962e'"
                                   onmouseout="this.style.backgroundColor='#D4AF37'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                                 -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver
                                </a>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                No hay aspirantes registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>{{-- /card --}}

        {{-- Volver al dashboard --}}
        <div class="mt-6">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 text-sm font-medium transition-colors duration-150"
               style="color: #0F4229;"
               onmouseover="this.style.textDecoration='underline'"
               onmouseout="this.style.textDecoration='none'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al panel
            </a>
        </div>

    </div>
</section>

@endsection
