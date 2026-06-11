@extends('layouts.app')

@section('title', 'Capturar calificaciones | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <a href="{{ route('profesor.calificaciones.index') }}"
               class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4 w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Mis materias
            </a>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Captura de calificaciones</p>
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $carga->materia->nombre }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Grupo: <strong>{{ $carga->grupo->clave }}</strong> &nbsp;·&nbsp;
                Período: <strong>{{ $carga->periodo->nombre ?? '—' }}</strong> &nbsp;·&nbsp;
                Cuatrimestre: <strong>{{ $carga->grupo->cuatrimestre }}</strong>
            </p>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        @if ($alumnos->isEmpty())
            <div class="bg-white rounded-2xl shadow-md p-12 text-center text-gray-400 text-sm">
                No hay alumnos activos en este grupo.
            </div>
        @else

        {{-- Indicaciones --}}
        <div class="flex items-start gap-3 bg-white border border-gray-200 rounded-xl px-5 py-3 mb-6">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs text-gray-500">
                Escribe la calificación en cada celda (ej. <code class="bg-gray-100 px-1 rounded">8</code> o <code class="bg-gray-100 px-1 rounded">8.5</code>).
                El <strong class="text-gray-700">Extraordinario</strong> solo está disponible cuando ambos parciales ya están guardados.
                Mínimo aprobatorio: <strong class="text-gray-700">7.0</strong>.
            </p>
        </div>

        <form method="POST" action="{{ route('profesor.calificaciones.guardar', $carga->id) }}">
            @csrf

            <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Alumnos del grupo</h2>
                    <span class="text-xs text-gray-400">{{ $alumnos->count() }} alumno{{ $alumnos->count() !== 1 ? 's' : '' }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Alumno</th>
                                <th class="px-6 py-3">Matrícula</th>
                                <th class="px-6 py-3 text-center">Parcial 1</th>
                                <th class="px-6 py-3 text-center">Parcial 2</th>
                                <th class="px-6 py-3 text-center">Extraordinario</th>
                                <th class="px-6 py-3 text-center">Cal. Final</th>
                                <th class="px-6 py-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($alumnos as $alumno)
                            @php
                                $p1Key = $alumno->id . '-parcial-1';
                                $p2Key = $alumno->id . '-parcial-2';
                                $exKey = $alumno->id . '-extraordinario-1';
                                $p1    = $calificaciones[$p1Key]->first() ?? null;
                                $p2    = $calificaciones[$p2Key]->first() ?? null;
                                $ex    = $calificaciones[$exKey]->first() ?? null;
                                $tieneParciales = $p1 && $p2;
                                $calFinal = $ex
                                    ? $ex->calificacion
                                    : ($tieneParciales ? round(($p1->calificacion + $p2->calificacion) / 2, 1) : null);
                                $aprobado = $calFinal !== null && $calFinal >= 7.0;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-100">
                                <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                    {{ $alumno->nombre_completo }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500 whitespace-nowrap">
                                    {{ $alumno->matricula }}
                                </td>

                                {{-- Parcial 1 --}}
                                <td class="px-6 py-3 text-center">
                                    <input type="number"
                                           name="grades[{{ $alumno->id }}][parcial][1]"
                                           step="0.1" min="0" max="10"
                                           value="{{ $p1?->calificacion }}"
                                           placeholder="—"
                                           class="w-20 text-center text-sm border rounded-lg px-2 py-1.5 focus:outline-none transition-colors
                                                  {{ $p1 ? ($p1->calificacion >= 7.0 ? 'border-green-400 bg-green-50 text-green-700' : 'border-red-400 bg-red-50 text-red-700') : 'border-gray-300 bg-white text-gray-700' }}"
                                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                                           onblur="this.style.boxShadow=''">
                                </td>

                                {{-- Parcial 2 --}}
                                <td class="px-6 py-3 text-center">
                                    <input type="number"
                                           name="grades[{{ $alumno->id }}][parcial][2]"
                                           step="0.1" min="0" max="10"
                                           value="{{ $p2?->calificacion }}"
                                           placeholder="—"
                                           class="w-20 text-center text-sm border rounded-lg px-2 py-1.5 focus:outline-none transition-colors
                                                  {{ $p2 ? ($p2->calificacion >= 7.0 ? 'border-green-400 bg-green-50 text-green-700' : 'border-red-400 bg-red-50 text-red-700') : 'border-gray-300 bg-white text-gray-700' }}"
                                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                                           onblur="this.style.boxShadow=''">
                                </td>

                                {{-- Extraordinario --}}
                                <td class="px-6 py-3 text-center">
                                    @if ($tieneParciales)
                                        <input type="number"
                                               name="grades[{{ $alumno->id }}][extraordinario][1]"
                                               step="0.1" min="0" max="10"
                                               value="{{ $ex?->calificacion }}"
                                               placeholder="—"
                                               class="w-20 text-center text-sm border rounded-lg px-2 py-1.5 focus:outline-none transition-colors
                                                      {{ $ex ? ($ex->calificacion >= 7.0 ? 'border-green-400 bg-green-50 text-green-700' : 'border-red-400 bg-red-50 text-red-700') : 'border-gray-300 bg-white text-gray-700' }}"
                                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                                               onblur="this.style.boxShadow=''">
                                    @else
                                        <span class="text-xs text-gray-300 italic">Pendiente parciales</span>
                                    @endif
                                </td>

                                {{-- Calificación final --}}
                                <td class="px-6 py-4 text-center font-bold text-sm whitespace-nowrap">
                                    @if ($calFinal !== null)
                                        <span class="{{ $aprobado ? 'text-green-700' : 'text-red-600' }}">
                                            {{ number_format($calFinal, 1) }}
                                            @if ($ex)
                                                <span class="block text-xs font-normal text-gray-400">(extra)</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if ($calFinal !== null)
                                        @if ($aprobado)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                                  style="background-color: #0F4229;">
                                                Aprobado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                Reprobado
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Botón guardar --}}
            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3 rounded-xl text-sm font-bold text-white shadow-sm transition-colors"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar calificaciones
                </button>
            </div>

        </form>
        @endif

    </div>
</section>
@endsection
