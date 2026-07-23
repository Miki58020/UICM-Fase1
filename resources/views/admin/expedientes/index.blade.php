@extends('layouts.app')

@section('title', 'Expedientes | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Control Escolar
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Expedientes de alumnos</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Búsqueda y filtros --}}
        <form method="GET" action="{{ route('admin.expedientes.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">

            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Buscar por nombre o matrícula…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>

            <div class="relative sm:w-60">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <select name="programa"
                        onchange="this.form.submit()"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los programas</option>
                    @foreach($programas as $programa)
                        <option value="{{ $programa->id }}" @selected(request('programa') == $programa->id)>{{ $programa->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        {{-- Tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Listado de alumnos</h2>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green">{{ $alumnos->total() }} registros</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Alumno</th>
                            <th class="px-6 py-3">Matrícula</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3">Grupo</th>
                            <th class="px-6 py-3 text-center">Cuatrimestre</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($alumnos as $alumno)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full flex-shrink-0 overflow-hidden border-2"
                                         style="border-color: #0F4229;">
                                        @if($alumno->user?->foto)
                                            <img src="{{ route('admin.archivo', ['path' => $alumno->user->foto]) }}"
                                                 alt="Foto de perfil"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-white text-xs font-bold"
                                                 style="background-color: #0F4229;">
                                                {{ strtoupper(substr($alumno->nombre, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $alumno->nombre_completo }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-700 text-sm">
                                {{ $alumno->matricula }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $alumno->programa->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($alumno->grupo)
                                <span class="font-mono text-xs font-bold" style="color: #0F4229;">{{ $alumno->grupo->clave }}</span>
                                @else
                                <span class="text-gray-400 text-sm">Sin grupo</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($alumno->cuatrimestre_actual)
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white"
                                      style="background-color: #D4AF37;">
                                    {{ $alumno->cuatrimestre_actual }}°
                                </span>
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $colorEstado = match($alumno->estado) {
                                        'activo'   => '#0F4229',
                                        'inactivo' => '#EFAD5A',
                                        'baja'     => '#dc2626',
                                        default    => '#9ca3af',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white capitalize"
                                      style="background-color: {{ $colorEstado }};">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                    {{ $alumno->estado }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('admin.expedientes.show', $alumno) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                   style="background-color: #D4AF37;"
                                   onmouseover="this.style.backgroundColor='#b8962e'"
                                   onmouseout="this.style.backgroundColor='#D4AF37'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver expediente
                                </a>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                                    <p class="text-sm text-gray-500 max-w-xs mx-auto">{{ request('q') || request('programa') ? 'No se encontraron alumnos con ese criterio.' : 'No hay alumnos registrados.' }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($alumnos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex-shrink-0">
                {{ $alumnos->links() }}
            </div>
            @endif
        </div>

    </div>
</section>

@endsection
