@extends('layouts.app')

@section('title', 'Materias | UICM')

@section('content')
@php
    $conteoPorEstado = [
        'calificada'  => $carga->where('estado_revision', 'aprobado')->count(),
        'en_revision' => $carga->where('estado_revision', 'pendiente')->count(),
        'en_curso'    => $carga->whereNotIn('estado_revision', ['aprobado', 'pendiente'])->count(),
        'todas'       => $carga->count(),
    ];
@endphp
<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{
             busqueda: '',
             filtroEstado: '',
             filtrar() {
                 this.$nextTick(() => {
                     const q = this.busqueda.toLowerCase();
                     const filas = this.$refs.tbody.querySelectorAll('tr[data-materia]');
                     let visibles = 0;
                     filas.forEach(f => {
                         const pasaBusq = !q
                             || f.dataset.materia.includes(q)
                             || f.dataset.profesor.includes(q)
                             || f.dataset.clave.includes(q);
                         const pasaEstado = !this.filtroEstado || f.dataset.estado === this.filtroEstado;
                         const mostrar = pasaBusq && pasaEstado;
                         f.style.display = mostrar ? '' : 'none';
                         if (mostrar) visibles++;
                     });
                     this.$refs.contador.textContent = visibles + ' ' + (visibles === 1 ? 'materia' : 'materias');
                     this.$refs.sinResultados.style.display = (visibles === 0 && {{ $conteoPorEstado['todas'] }} > 0) ? '' : 'none';
                 });
             }
         }"
         x-init="filtrar()">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Alumno</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Materias</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Tarjetas resumen (también filtran por estado) --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <button type="button" @click="filtroEstado = ''; filtrar()"
                    :class="filtroEstado === '' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #6B7280;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-600">{{ $conteoPorEstado['todas'] }}</p>
            </button>
            <button type="button" @click="filtroEstado = 'aprobado'; filtrar()"
                    :class="filtroEstado === 'aprobado' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Calificadas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $conteoPorEstado['calificada'] }}</p>
            </button>
            <button type="button" @click="filtroEstado = 'pendiente'; filtrar()"
                    :class="filtroEstado === 'pendiente' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">En revisión</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $conteoPorEstado['en_revision'] }}</p>
            </button>
            <button type="button" @click="filtroEstado = 'en_curso'; filtrar()"
                    :class="filtroEstado === 'en_curso' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #9ca3af;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">En curso</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-400">{{ $conteoPorEstado['en_curso'] }}</p>
            </button>
        </div>

        {{-- Buscador y filtro por estado --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" x-model="busqueda" @input="filtrar()"
                       placeholder="Buscar por materia, profesor o clave…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div class="relative sm:w-56">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <select x-model="filtroEstado" @change="filtrar()"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los estados</option>
                    <option value="aprobado">Calificada</option>
                    <option value="pendiente">En revisión</option>
                    <option value="en_curso">En curso</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                             C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                             C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                             C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700">Materias asignadas</h2>
                <span class="ml-auto text-xs text-gray-400 text-right">
                    <span x-ref="contador">{{ $carga->count() }} {{ $carga->count() === 1 ? 'materia' : 'materias' }}</span>
                    · {{ $totalCreditos }} créditos · {{ $alumno->cuatrimestre_actual }}° cuatrimestre · {{ $alumno->grupo->clave ?? '—' }}
                </span>
            </div>

            <div class="overflow-x-auto">
                @if ($carga->isEmpty())
                    <div class="px-6 py-10">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                            <p class="text-sm text-gray-500 max-w-xs mx-auto">No hay materias asignadas. El grupo aún no tiene carga académica registrada.</p>
                        </div>
                    </div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Clave</th>
                            <th class="px-6 py-3">Materia</th>
                            <th class="px-6 py-3">Profesor</th>
                            <th class="px-6 py-3">Horario</th>
                            <th class="px-6 py-3">Aula virtual</th>
                            <th class="px-6 py-3 text-center">Créditos</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tbody">
                        <tr x-ref="sinResultados" style="display: none;">
                            <td colspan="7" class="px-6 py-10">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                                    <p class="text-sm text-gray-500 max-w-xs mx-auto">Ninguna materia coincide con la búsqueda o los filtros.</p>
                                </div>
                            </td>
                        </tr>
                        @foreach ($carga as $c)
                        @php
                            $estadoBadge = match ($c->estado_revision) {
                                'aprobado'  => ['#0F4229', 'Calificada', 'aprobado'],
                                'pendiente' => ['#EFAD5A', 'En revisión', 'pendiente'],
                                default     => ['#9ca3af', 'En curso', 'en_curso'],
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-100"
                            data-materia="{{ strtolower($c->materia->nombre) }}"
                            data-profesor="{{ strtolower($c->profesor->nombre ?? '') }}"
                            data-clave="{{ strtolower($c->materia->clave) }}"
                            data-estado="{{ $estadoBadge[2] }}">

                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-500 whitespace-nowrap">
                                {{ $c->materia->clave }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                {{ $c->materia->nombre }}
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
                                    {{ $c->horario_formateado ?? '—' }}
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

                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white"
                                      style="background-color: #D4AF37;">
                                    {{ $c->materia->creditos }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white whitespace-nowrap"
                                      style="background-color: {{ $estadoBadge[0] }};">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                    {{ $estadoBadge[1] }}
                                </span>
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
