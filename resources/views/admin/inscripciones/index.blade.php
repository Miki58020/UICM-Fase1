@extends('layouts.app')

@section('title', 'Generación de Matrículas | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado de módulo --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Control Escolar
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Aspirantes listos para inscripción</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>


        {{-- Contadores (también son las pestañas) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">

            <a href="{{ route('admin.inscripciones.index', ['vista' => 'pendientes']) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 block" style="border-color: #EFAD5A; {{ $vista === 'pendientes' ? 'box-shadow: 0 0 0 2px #EFAD5A;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pendientes de acceso</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">
                    {{ $conteo['listos'] }}
                </p>
            </a>

            <a href="{{ route('admin.inscripciones.index', ['vista' => 'generados']) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 block" style="border-color: #0F4229; {{ $vista === 'generados' ? 'box-shadow: 0 0 0 2px #0F4229;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Acceso generado</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">
                    {{ $conteo['generados'] }}
                </p>
            </a>

        </div>

        {{-- Búsqueda --}}
        <form method="GET" action="{{ route('admin.inscripciones.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
            <input type="hidden" name="vista" value="{{ $vista }}">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Buscar por folio, nombre o matrícula…"
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
                <select name="programa" onchange="this.form.submit()"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los programas</option>
                    @foreach($programas as $p)
                        <option value="{{ $p->id }}" @selected(request('programa') == $p->id)>{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        {{-- Card tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">
                    {{ $vista === 'pendientes' ? 'Aspirantes pendientes de acceso' : 'Alumnos con acceso generado' }}
                </h2>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green">
                    {{ $vista === 'pendientes' ? $listos->count() : $generados->total() }} registros
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Folio</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3">Matrícula</th>
                            <th class="px-6 py-3">Grupo</th>
                            <th class="px-6 py-3 text-center">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @if ($vista === 'pendientes')
                        @forelse ($listos as $asp)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">

                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-600 whitespace-nowrap">
                                {{ $asp->folio }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                                {{ $asp->nombre_completo }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $asp->programa->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4 font-mono text-xs font-semibold whitespace-nowrap" style="color: #0F4229;">
                                {{ $asp->alumno->matricula ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-xs text-gray-400">
                                <span class="italic">Automático</span>
                            </td>

                            {{-- Acción --}}
                            <td class="px-6 py-4 text-center">
                                <form method="POST"
                                      action="{{ route('admin.inscripciones.inscribir', $asp->alumno->id) }}"
                                      onsubmit="return confirm('¿Generar acceso al portal para {{ addslashes($asp->nombre_completo) }}? Se asignará grupo automáticamente y se enviará el correo de bienvenida.')">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-white
                                                   transition-colors duration-150 whitespace-nowrap"
                                            style="background-color: #0F4229;"
                                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                                            onmouseout="this.style.backgroundColor='#0F4229'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                                     a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964
                                                     A6 6 0 1121 9z"/>
                                        </svg>
                                        Generar acceso
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                                    <p class="text-sm text-gray-500 max-w-xs mx-auto">{{ request('q') || request('programa') ? 'No se encontraron registros con ese criterio.' : 'No hay aspirantes pendientes de acceso.' }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        @else
                        @forelse ($generados as $alumno)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">

                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-600 whitespace-nowrap">
                                {{ $alumno->aspirante->folio ?? '—' }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                                {{ $alumno->nombre_completo }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $alumno->programa->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4 font-mono text-xs font-semibold whitespace-nowrap" style="color: #0F4229;">
                                {{ $alumno->matricula }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($alumno->grupo)
                                <span class="font-mono text-xs font-bold" style="color: #0F4229;">{{ $alumno->grupo->clave }}</span>
                                @else
                                <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>

                            {{-- Acción --}}
                            <td class="px-6 py-4 text-center">
                                <form method="POST"
                                      action="{{ route('admin.inscripciones.reenviar', $alumno->id) }}"
                                      onsubmit="return confirm('¿Reenviar credenciales de acceso a {{ addslashes($alumno->nombre_completo) }}?')">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-white
                                                   transition-colors duration-150 whitespace-nowrap"
                                            style="background-color: #EFAD5A;"
                                            onmouseover="this.style.backgroundColor='#d4922e'"
                                            onmouseout="this.style.backgroundColor='#EFAD5A'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Reenviar acceso
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                                    <p class="text-sm text-gray-500 max-w-xs mx-auto">{{ request('q') || request('programa') ? 'No se encontraron registros con ese criterio.' : 'No hay alumnos con acceso generado.' }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        @endif
                    </tbody>

                </table>
            </div>

            @if ($vista === 'generados' && $generados->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex-shrink-0">
                {{ $generados->links() }}
            </div>
            @endif

        </div>{{-- /card --}}


    </div>
</section>

@endsection
