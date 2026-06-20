@extends('layouts.app')

@section('title', 'Calificaciones | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Coordinación Académica
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Calificaciones por grupo</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        <div class="space-y-6">

            {{-- Card filtros --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #D4AF37;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414
                                 a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293
                                 A1 1 0 013 6.586V4z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Filtros de búsqueda</h2>
                </div>

                <div class="px-6 py-5">
                    <form method="GET" action="{{ route('admin.calificaciones.index') }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

                            {{-- Grupo (ocupa las 4 columnas en pantallas grandes como único filtro) --}}
                            <div class="sm:col-span-2 lg:col-span-3">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                    Grupo
                                </label>
                                <select name="grupo_id"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700
                                               outline-none transition-all duration-150 bg-white"
                                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 3px rgba(15,66,41,0.12)'"
                                        onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                                    <option value="">— Selecciona un grupo —</option>
                                    @foreach ($grupos as $g)
                                        <option value="{{ $g->id }}" {{ request('grupo_id') == $g->id ? 'selected' : '' }}>
                                            {{ $g->clave }}
                                            @if ($g->programa) — {{ $g->programa->nombre }} @endif
                                            @if ($g->periodo) ({{ $g->periodo->nombre }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white
                                           shadow-sm transition-all duration-150"
                                    style="background-color: #0F4229;"
                                    onmouseover="this.style.backgroundColor='#0a2e1c'"
                                    onmouseout="this.style.backgroundColor='#0F4229'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9
                                             m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Ver calificaciones
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sin grupo seleccionado --}}
            @if (!$grupo)
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4"
                         style="background-color: #f0f9f4;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                     M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Selecciona un grupo</h3>
                    <p class="text-sm text-gray-500 max-w-xs">
                        Elige el grupo en el selector de arriba y haz clic en "Ver calificaciones".
                    </p>
                </div>
            </div>

            @elseif ($cargasConCalif->isEmpty())

            {{-- Grupo sin carga académica --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #EFAD5A;"></div>
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4"
                         style="background-color: #fef4e8;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #EFAD5A;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin carga académica</h3>
                    <p class="text-sm text-gray-500 max-w-xs">
                        El grupo <strong>{{ $grupo->clave }}</strong> no tiene materias asignadas todavía.
                    </p>
                </div>
            </div>

            @else

            {{-- Tarjeta resumen del grupo --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                                     M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                                     m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-gray-700">
                            Grupo {{ $grupo->clave }}
                            @if ($grupo->programa) — {{ $grupo->programa->nombre }} @endif
                        </h2>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-white"
                          style="background-color: #0F4229;">
                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                        {{ $cargasConCalif->count() }} materia{{ $cargasConCalif->count() !== 1 ? 's' : '' }}
                    </span>
                </div>

                <div class="px-6 py-5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-uicm-gray rounded-xl p-3 text-center">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Periodo</p>
                            <p class="text-sm font-bold text-gray-800">{{ $grupo->periodo->nombre ?? '—' }}</p>
                        </div>
                        <div class="bg-uicm-gray rounded-xl p-3 text-center">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Cuatrimestre</p>
                            <p class="text-sm font-bold" style="color: #0F4229;">{{ $grupo->cuatrimestre }}°</p>
                        </div>
                        <div class="bg-uicm-gray rounded-xl p-3 text-center">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Alumnos</p>
                            <p class="text-sm font-bold" style="color: #0F4229;">{{ $grupo->alumnos->count() }}</p>
                        </div>
                        <div class="bg-uicm-gray rounded-xl p-3 text-center">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Materias</p>
                            <p class="text-sm font-bold" style="color: #D4AF37;">{{ $cargasConCalif->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla por materia --}}
            @foreach ($cargasConCalif as $carga)
            @php
                $alumnos = $grupo->alumnos;
                $estadoBadge = [
                    'pendiente' => ['bg-amber-100 text-amber-700', 'Pendiente de revisión'],
                    'aprobado'  => ['bg-green-100 text-green-700', 'Aprobado por control escolar'],
                    'rechazado' => ['bg-red-100 text-red-700', 'Rechazado'],
                ][$carga->estado_revision];
            @endphp
            <div class="bg-white rounded-2xl shadow-md overflow-hidden" x-data="{ rechazando: false }">

                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

                <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #D4AF37;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                                     C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                                     C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                                     C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">{{ $carga->materia->nombre ?? '—' }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Profesor: {{ $carga->profesor->nombre ?? 'Sin asignar' }}
                                @if ($carga->horario) &nbsp;·&nbsp; {{ $carga->horario }} @endif
                                @if ($carga->aula) &nbsp;·&nbsp; Aula {{ $carga->aula }} @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        @if ($carga->sospechosa)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                ⚠ Calificaciones idénticas
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $estadoBadge[0] }}">
                            {{ $estadoBadge[1] }}
                        </span>
                        @php
                            $conCalif = $carga->calificaciones->where('tipo', 'final')->pluck('alumno_id')->unique()->count();
                        @endphp
                        <span class="text-xs text-gray-400 whitespace-nowrap">
                            {{ $conCalif }}/{{ $alumnos->count() }} con calificación
                        </span>
                    </div>
                </div>

                @if ($carga->estado_revision === 'rechazado' && $carga->motivo_rechazo)
                <div class="px-6 py-3 bg-red-50 border-b border-red-100 text-xs text-red-700">
                    <strong>Motivo de rechazo:</strong> {{ $carga->motivo_rechazo }}
                </div>
                @endif

                @if ($conCalif > 0)
                <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-end gap-2" x-show="!rechazando">
                    <form method="POST" action="{{ route('admin.calificaciones.aprobar', $carga) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white"
                                style="background-color: #0F4229;">
                            Aprobar
                        </button>
                    </form>
                    <button type="button" @click="rechazando = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-red-100 text-red-700">
                        Rechazar
                    </button>
                </div>
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50" x-show="rechazando" x-cloak>
                    <form method="POST" action="{{ route('admin.calificaciones.rechazar', $carga) }}" class="flex flex-col gap-2">
                        @csrf
                        <label class="text-xs font-semibold text-gray-600">Motivo del rechazo</label>
                        <textarea name="motivo" required rows="2"
                                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none"
                                  placeholder="Ej. inconsistencias en las calificaciones del grupo"></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="rechazando = false"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600">Cancelar</button>
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl text-xs font-bold bg-red-600 text-white">Confirmar rechazo</button>
                        </div>
                    </form>
                </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Alumno</th>
                                <th class="px-6 py-3">Matrícula</th>
                                <th class="px-6 py-3 text-center">Final</th>
                                <th class="px-6 py-3 text-center">Extraordinario</th>
                                <th class="px-6 py-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($alumnos as $alumno)
                            @php
                                $califs   = $carga->calificaciones->where('alumno_id', $alumno->id);
                                $final    = $califs->first(fn($c) => $c->tipo === 'final');
                                $ex       = $califs->first(fn($c) => $c->tipo === 'extraordinario');
                                $calFinal = $ex ? $ex->calificacion : $final?->calificacion;
                                $aprobado = $calFinal !== null && $calFinal >= 7.0;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-100">
                                <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                    {{ $alumno->nombre_completo }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500 whitespace-nowrap">
                                    {{ $alumno->matricula }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold">
                                    @if ($final)
                                        <span class="{{ $final->calificacion >= 7.0 ? 'text-green-700' : 'text-red-600' }}">
                                            {{ number_format($final->calificacion, 1) }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-bold">
                                    @if ($ex)
                                        <span class="{{ $ex->calificacion >= 7.0 ? 'text-green-700' : 'text-red-600' }}">
                                            {{ number_format($ex->calificacion, 1) }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if ($calFinal !== null)
                                        @if ($aprobado)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                                  style="background-color: #0F4229;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                                Aprobado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>
                                                Reprobado
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                    No hay alumnos en este grupo.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

            @endif

        </div>{{-- /space-y-6 --}}

    </div>
</section>
@endsection
