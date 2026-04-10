@extends('layouts.app')

@section('title', 'Reinscripciones | UICM')

@section('content')

@php
    $gruposJson = $gruposActivos->map(fn($g) => [
        'id'         => $g->id,
        'clave'      => $g->clave,
        'programa_id'=> $g->programa_id,
    ])->values()->toJson();
@endphp

<section
    x-data="{
        busqueda: '',
        modalCompletar: false,
        alumnoId: null,
        alumnoNombre: '',
        alumnoPrograma: null,
        grupoSeleccionado: '',
        grupos: {{ $gruposJson }},
        gruposFiltrados() {
            return this.grupos.filter(g => g.programa_id == this.alumnoPrograma);
        },
        abrirCompletar(id, nombre, programaId) {
            this.alumnoId       = id;
            this.alumnoNombre   = nombre;
            this.alumnoPrograma = programaId;
            this.grupoSeleccionado = '';
            this.modalCompletar = true;
        },
        filtrar() {
            this.$nextTick(() => {
                const q = this.busqueda.toLowerCase();
                let vis = 0;
                this.$refs.tbody.querySelectorAll('tr[data-nombre]').forEach(f => {
                    const ok = !q || f.dataset.nombre.toLowerCase().includes(q) || f.dataset.matricula.toLowerCase().includes(q);
                    f.style.display = ok ? '' : 'none';
                    if (ok) vis++;
                });
                this.$refs.sinResultados.style.display = vis === 0 ? '' : 'none';
                this.$refs.contador.textContent = vis + ' alumno' + (vis !== 1 ? 's' : '');
            });
        }
    }"
    class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Control Escolar
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Reinscripciones</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Alerta sin periodo activo --}}
        @if (!$periodoActivo)
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-yellow-50" style="border-color: #EFAD5A;">
                <p class="text-sm font-semibold text-yellow-800">
                    No hay un período académico activo. Activa un período en el módulo de Periodos para poder gestionar reinscripciones.
                </p>
            </div>
        @else
            <div class="mb-6 rounded-xl px-5 py-3 bg-white border border-gray-200 flex items-center gap-3 shadow-sm">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: #0F4229;"></span>
                <span class="text-sm font-medium text-gray-700">
                    Período activo: <strong>{{ $periodoActivo->label }}</strong>
                    ({{ $periodoActivo->fecha_inicio_registro->format('d/m/Y') }} — {{ $periodoActivo->fecha_fin_registro->format('d/m/Y') }})
                </span>
            </div>
        @endif

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-green-50" style="border-color: #0F4229;">
                <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-red-50 border-red-400">
                <p class="text-sm font-semibold text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Contadores --}}
        @php
            $sinCobro          = 0;
            $enValidacion      = 0;
            $pendienteCompletar= 0;
            $completadas       = 0;

            foreach ($alumnos as $a) {
                $pr = $a->reinscripciones->first();
                $enPeriodoActivo = $periodoActivo && $a->grupo && $a->grupo->periodo_id === $periodoActivo->id;
                if (!$pr || $pr->estado === 'rechazado')            $sinCobro++;
                elseif ($pr->estado === 'pendiente')                 $enValidacion++;
                elseif ($pr->estado === 'aprobado' && !$enPeriodoActivo) $pendienteCompletar++;
                elseif ($pr->estado === 'aprobado' && $enPeriodoActivo)  $completadas++;
            }
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left" style="border-color: #6B7280;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total activos</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-600">{{ $alumnos->count() }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left" style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Sin cobro</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $sinCobro }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left" style="border-color: #3b82f6;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">En validación</p>
                <p class="text-2xl font-extrabold mt-1 text-blue-500">{{ $enValidacion }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Completadas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $completadas }}</p>
            </div>

        </div>

        {{-- Pendientes de completar banner --}}
        @if ($pendienteCompletar > 0)
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-amber-50" style="border-color: #D4AF37;">
                <p class="text-sm font-semibold text-amber-800">
                    {{ $pendienteCompletar }} alumno{{ $pendienteCompletar !== 1 ? 's' : '' }} con pago aprobado pendiente{{ $pendienteCompletar !== 1 ? 's' : '' }} de asignación de grupo.
                </p>
            </div>
        @endif

        {{-- Búsqueda --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" x-model="busqueda" @input="filtrar()"
                       placeholder="Buscar por nombre o matrícula…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
        </div>

        {{-- Tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Alumnos activos</h2>
                <span class="text-xs text-gray-400" x-ref="contador">{{ $alumnos->count() }} alumnos</span>
            </div>

            <div class="overflow-x-auto">
                @if ($alumnos->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">
                        No hay alumnos activos registrados.
                    </div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Alumno</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3">Grupo actual</th>
                            <th class="px-6 py-3">Cuatrimestre</th>
                            <th class="px-6 py-3">Estado reinscripción</th>
                            <th class="px-6 py-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tbody">
                        @foreach ($alumnos as $alumno)
                        @php
                            $pagoReinsc   = $alumno->reinscripciones->first();
                            $enPeriodoActivo = $periodoActivo && $alumno->grupo && $alumno->grupo->periodo_id === $periodoActivo->id;

                            if (!$pagoReinsc || $pagoReinsc->estado === 'rechazado')
                                $estadoReinsc = 'sin_cobro';
                            elseif ($pagoReinsc->estado === 'pendiente')
                                $estadoReinsc = 'pendiente';
                            elseif ($pagoReinsc->estado === 'aprobado' && !$enPeriodoActivo)
                                $estadoReinsc = 'pendiente_completar';
                            else
                                $estadoReinsc = 'completada';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-100"
                            data-nombre="{{ strtolower($alumno->nombre_completo) }}"
                            data-matricula="{{ strtolower($alumno->matricula) }}">

                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-semibold text-gray-800">{{ $alumno->nombre_completo }}</p>
                                <p class="text-xs font-mono text-gray-400 mt-0.5">{{ $alumno->matricula }}</p>
                            </td>

                            <td class="px-6 py-4 text-gray-600 text-xs">
                                {{ $alumno->programa?->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($alumno->grupo)
                                    <span class="font-mono font-semibold text-gray-700">{{ $alumno->grupo->clave }}</span>
                                    <span class="ml-1 text-xs text-gray-400">{{ $alumno->grupo->periodo?->nombre }}</span>
                                @else
                                    <span class="text-gray-300">Sin grupo</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white"
                                      style="background-color: #EFAD5A;">
                                    {{ $alumno->cuatrimestre_actual }}°
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($estadoReinsc === 'sin_cobro')
                                    @if ($pagoReinsc && $pagoReinsc->estado === 'rechazado')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                            Pago rechazado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-500 bg-gray-100">
                                            Sin cobro generado
                                        </span>
                                    @endif
                                @elseif ($estadoReinsc === 'pendiente')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #EFAD5A;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        En validación
                                    </span>
                                @elseif ($estadoReinsc === 'pendiente_completar')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #3b82f6;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Pago aprobado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Completada
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if ($estadoReinsc === 'sin_cobro')
                                    @if ($periodoActivo)
                                        <form method="POST"
                                              action="{{ route('admin.reinscripciones.generar-pago', $alumno) }}"
                                              onsubmit="return confirm('¿Generar cobro de reinscripción para {{ addslashes($alumno->nombre_completo) }}?')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors"
                                                    style="background-color: #D4AF37;"
                                                    onmouseover="this.style.backgroundColor='#b8962e'"
                                                    onmouseout="this.style.backgroundColor='#D4AF37'">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                Generar cobro
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-300">Sin período activo</span>
                                    @endif
                                @elseif ($estadoReinsc === 'pendiente_completar')
                                    <button type="button"
                                            @click="abrirCompletar({{ $alumno->id }}, '{{ addslashes($alumno->nombre_completo) }}', {{ $alumno->programa_id ?? 'null' }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors"
                                            style="background-color: #0F4229;"
                                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                                            onmouseout="this.style.backgroundColor='#0F4229'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Completar
                                    </button>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>

                        </tr>
                        @endforeach

                        <tr x-ref="sinResultados" style="display:none;">
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                                No se encontraron alumnos con ese criterio.
                            </td>
                        </tr>
                    </tbody>
                </table>
                @endif
            </div>
        </div>

    </div>

    {{-- Modal: Completar reinscripción --}}
    <div x-show="modalCompletar"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background-color: rgba(0,0,0,0.5); display: none;"
         @click.self="modalCompletar = false">

        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0
                                 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946
                                 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138
                                 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806
                                 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438
                                 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <h2 class="text-sm font-bold text-gray-800">Completar reinscripción</h2>
                </div>
                <button type="button" @click="modalCompletar = false"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400
                               hover:text-gray-600 hover:bg-gray-100 transition-colors duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 pt-4 pb-2">
                <p class="text-sm text-gray-600">
                    Asignar nuevo grupo a <span class="font-semibold text-gray-800" x-text="alumnoNombre"></span>.
                    El cuatrimestre se incrementará automáticamente.
                </p>
            </div>

            <form method="POST" :action="'/admin/reinscripciones/' + alumnoId + '/completar'"
                  class="px-6 py-4 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Grupo del período activo <span class="text-red-500">*</span>
                    </label>
                    <select name="grupo_id" x-model="grupoSeleccionado" required
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                            onblur="this.style.borderColor=''; this.style.boxShadow=''">
                        <option value="">— Seleccionar grupo —</option>
                        <template x-for="g in gruposFiltrados()" :key="g.id">
                            <option :value="g.id" x-text="g.clave"></option>
                        </template>
                    </select>
                    <p class="mt-1 text-xs text-gray-400"
                       x-show="alumnoPrograma && gruposFiltrados().length === 0">
                        No hay grupos en el período activo para este programa.
                    </p>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="modalCompletar = false"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold border-2 transition-colors duration-150"
                            style="border-color: #0F4229; color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#f0f9f4'"
                            onmouseout="this.style.backgroundColor='transparent'">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-200"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        Completar reinscripción
                    </button>
                </div>
            </form>

        </div>
    </div>

</section>

@endsection
