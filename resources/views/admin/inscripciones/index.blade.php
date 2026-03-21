@extends('layouts.app')

@section('title', 'Generación de Matrículas | UICM')

@section('content')

<section
    x-data="{
        busqueda: '',
        filtrar() {
            this.$nextTick(() => {
                const filas = this.$refs.tbody.querySelectorAll('tr[data-nombre]');
                let visibles = 0;
                filas.forEach(f => {
                    const texto = this.busqueda.toLowerCase();
                    const mostrar = !texto || f.dataset.nombre.toLowerCase().includes(texto) || f.dataset.folio.toLowerCase().includes(texto) || f.dataset.programa.toLowerCase().includes(texto);
                    f.style.display = mostrar ? '' : 'none';
                    if (mostrar) visibles++;
                });
                this.$refs.sinResultados.style.display = visibles === 0 ? '' : 'none';
                this.$refs.contadorVisible.textContent = visibles + ' registro' + (visibles !== 1 ? 's' : '');
            });
        }
    }"
    class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado de módulo --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Control Escolar
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Aspirantes listos para inscripción</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Flashes --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-green-50" style="border-color: #0F4229;">
                <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-red-50" style="border-color: #ef4444;">
                <p class="text-sm font-semibold text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Contadores --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Listos para inscribir</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">
                    {{ $listos->count() }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pago validado</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">
                    {{ $listos->count() }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Matrículas generadas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $inscritos }}</p>
            </div>

        </div>

        {{-- Búsqueda --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" x-model="busqueda" @input="filtrar()"
                       placeholder="Buscar por folio, nombre o programa…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
        </div>

        {{-- Card tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">
                    Aspirantes aprobados con pago validado
                </h2>
                <span class="text-xs text-gray-400" x-ref="contadorVisible">{{ $listos->count() }} registros</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Folio</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3">Matrícula</th>
                            <th class="px-6 py-3 text-center">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100" x-ref="tbody">
                        @forelse ($listos as $asp)
                        <tr class="hover:bg-gray-50 transition-colors duration-100"
                            data-folio="{{ strtolower($asp->folio) }}"
                            data-nombre="{{ strtolower($asp->nombre_completo) }}"
                            data-programa="{{ strtolower($asp->programa->nombre ?? '') }}">

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

                            {{-- Acción --}}
                            <td class="px-6 py-4 text-center">
                                <form method="POST"
                                      action="{{ route('admin.inscripciones.inscribir', $asp->alumno->id) }}"
                                      onsubmit="return confirm('¿Generar acceso al portal para {{ addslashes($asp->nombre_completo) }}?')">
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
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                No hay aspirantes pendientes de inscripción.
                            </td>
                        </tr>
                        @endforelse
                        <tr x-ref="sinResultados" style="display:none;">
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                No se encontraron registros con ese criterio.
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

        </div>{{-- /card --}}

        {{-- Volver --}}
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
