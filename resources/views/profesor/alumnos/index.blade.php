@extends('layouts.app')

@section('title', 'Alumnos | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{
             busqueda: '',
             filtrar() {
                 this.$nextTick(() => {
                     const q = this.busqueda.toLowerCase();
                     const filas = this.$refs.tbody.querySelectorAll('tr[data-alumno]');
                     let visibles = 0;
                     filas.forEach(f => {
                         const pasa = !q
                             || f.dataset.alumno.includes(q)
                             || f.dataset.matricula.includes(q)
                             || f.dataset.grupo.includes(q);
                         f.style.display = pasa ? '' : 'none';
                         if (pasa) visibles++;
                     });
                     this.$refs.contador.textContent = visibles + ' ' + (visibles === 1 ? 'alumno' : 'alumnos');
                     this.$refs.sinResultados.style.display = (visibles === 0 && {{ $alumnos->count() }} > 0) ? '' : 'none';
                 });
             }
         }"
         x-init="filtrar()">
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
            {{-- Tarjetas KPI --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
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
                        <p class="text-4xl font-extrabold leading-none mb-1" style="color: #EFAD5A;">{{ $alumnos->count() }}</p>
                        <p class="text-xs font-medium text-gray-500">Alumnos activos</p>
                    </div>
                </div>
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
                        <p class="text-4xl font-extrabold leading-none mb-1" style="color: #0F4229;">{{ $totalGrupos }}</p>
                        <p class="text-xs font-medium text-gray-500">Grupos</p>
                    </div>
                </div>
            </div>

            {{-- Buscador --}}
            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" x-model="busqueda" @input="filtrar()"
                           placeholder="Buscar por nombre, matrícula o grupo…"
                           class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>
                @unless ($grupoFiltro)
                    <div class="relative sm:w-56">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <select onchange="if(this.value){window.location.href='{{ route('profesor.alumnos.index') }}?grupo_id='+this.value}"
                                class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                                onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                                onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                            <option value="">Todos los grupos</option>
                            @foreach ($gruposDisponibles as $grupo)
                                <option value="{{ $grupo->id }}">{{ $grupo->clave }}</option>
                            @endforeach
                        </select>
                    </div>
                @endunless
            </div>

            @if ($grupoFiltro)
                <div class="mb-6 rounded-xl px-5 py-3 bg-white border border-gray-200 flex items-center gap-3 shadow-sm flex-wrap">
                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: #0F4229;"></span>
                    <span class="text-sm font-medium text-gray-700">
                        Mostrando alumnos del grupo <strong>{{ $grupoFiltro->clave }}</strong>
                    </span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Alumnos</h2>
                    <span class="ml-auto text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green" x-ref="contador">
                        {{ $alumnos->count() }} {{ $alumnos->count() === 1 ? 'alumno' : 'alumnos' }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Alumno</th>
                                <th class="px-6 py-3">Grupo</th>
                                <th class="px-6 py-3">Materias que le impartes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" x-ref="tbody">
                            <tr x-ref="sinResultados" style="display: none;">
                                <td colspan="3" class="px-6 py-10">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                                        <p class="text-sm text-gray-500 max-w-xs mx-auto">Ningún alumno coincide con la búsqueda.</p>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($alumnos as $a)
                            <tr class="hover:bg-gray-50"
                                data-alumno="{{ strtolower($a->alumno->nombre_completo) }}"
                                data-matricula="{{ strtolower($a->alumno->matricula) }}"
                                data-grupo="{{ strtolower($a->grupo->clave ?? '') }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex-shrink-0 overflow-hidden border-2"
                                             style="border-color: #0F4229;">
                                            @if($a->alumno->user?->foto)
                                                <img src="{{ route('admin.archivo', ['path' => $a->alumno->user->foto]) }}"
                                                     alt="Foto de perfil"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-white text-xs font-bold"
                                                     style="background-color: #0F4229;">
                                                    {{ strtoupper(substr($a->alumno->nombre, 0, 1)) }}
                                                </div>
                                            @endif
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
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-uicm-gold-soft text-uicm-gold-soft-text whitespace-nowrap">
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

            @if ($grupoFiltro)
                <div class="flex justify-start mt-6">
                    <a href="{{ route('profesor.alumnos.index') }}"
                       class="group inline-flex items-center gap-3 px-4 py-3 rounded-xl bg-white shadow-sm border border-gray-200 hover:border-gray-300 hover:shadow transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-500 group-hover:text-gray-700">Ver todos mis alumnos</span>
                    </a>
                </div>
            @endif
        @endif

    </div>
</section>
@endsection
