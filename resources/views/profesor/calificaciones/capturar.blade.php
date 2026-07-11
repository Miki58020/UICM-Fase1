@extends('layouts.app')

@section('title', 'Capturar calificaciones | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{ confirmarEnvio: false }">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Captura de calificaciones</p>
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $carga->materia->nombre }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Grupo: <strong>{{ $carga->grupo->clave }}</strong> &nbsp;·&nbsp;
                Período: <strong>{{ $carga->periodo->nombre ?? '—' }}</strong> &nbsp;·&nbsp;
                Cuatrimestre: <strong>{{ $carga->grupo->cuatrimestre }}</strong>
                @if ($carga->fecha_inicio && $carga->fecha_fin)
                    &nbsp;·&nbsp; Ventana: <strong>{{ $carga->fecha_inicio->format('d/m/Y') }} - {{ $carga->fecha_fin->format('d/m/Y') }}</strong>
                @endif
            </p>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        @if ($alumnos->isEmpty())
            <div class="bg-white rounded-2xl shadow-md p-12 text-center text-gray-400 text-sm">
                No hay alumnos activos en este grupo.
            </div>
        @else

        @if ($carga->estado_revision === 'rechazado')
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-5 py-3 mb-6">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-xs text-red-700">
                Control escolar rechazó estas calificaciones{{ $carga->motivo_rechazo ? ': '.$carga->motivo_rechazo : '.' }}
                Puedes volver a capturarlas aunque la ventana de fechas ya haya cerrado.
            </p>
        </div>
        @elseif (!$puedeCapturar)
        <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 mb-6">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs text-amber-700">
                La ventana de captura para esta materia ya cerró. Si necesitas corregir una calificación,
                usa el botón <strong>Solicitar aclaración</strong> en la fila del alumno.
            </p>
        </div>
        @endif

        {{-- Indicaciones de flujo --}}
        @if ($puedeCapturar)
        <div class="flex items-start gap-3 bg-white border border-gray-200 rounded-xl px-5 py-3 mb-4">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs text-gray-500">
                Escribe la calificación en cada celda (ej. <code class="bg-gray-100 px-1 rounded">8</code> o <code class="bg-gray-100 px-1 rounded">8.5</code>).
                El <strong class="text-gray-700">Extraordinario</strong> solo está disponible cuando la calificación final ya está guardada y es reprobatoria.
                Mínimo aprobatorio: <strong class="text-gray-700">7.0</strong>.
            </p>
        </div>
        <div class="flex items-start gap-3 rounded-xl px-5 py-3 mb-6 border"
             style="background-color: #f0f9f4; border-color: #a7d7b8;">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 style="color: #0F4229;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs" style="color: #0F4229;">
                <strong>Flujo de captura:</strong>
                Guarda el borrador tantas veces como necesites mientras capturas.
                Cuando termines, usa <strong>"Enviar a revisión"</strong> para que control escolar pueda revisar y aprobar las calificaciones.
                Las calificaciones <strong>no son visibles para los alumnos</strong> hasta que control escolar las apruebe.
            </p>
        </div>
        @else
        <div class="flex items-start gap-3 bg-white border border-gray-200 rounded-xl px-5 py-3 mb-6">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs text-gray-500">
                El <strong class="text-gray-700">Extraordinario</strong> solo está disponible cuando la calificación final ya está guardada y es reprobatoria.
                Mínimo aprobatorio: <strong class="text-gray-700">7.0</strong>.
            </p>
        </div>
        @endif

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
                                <th class="px-6 py-3 text-center">Final</th>
                                <th class="px-6 py-3 text-center">Extraordinario</th>
                                <th class="px-6 py-3 text-center">Estado</th>
                                @if (!$puedeCapturar)
                                    <th class="px-6 py-3 text-center">Aclaración</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($alumnos as $alumno)
                            @php
                                $finalKey = $alumno->id . '-final';
                                $exKey    = $alumno->id . '-extraordinario';
                                $final    = $calificaciones->get($finalKey)?->first();
                                $ex       = $calificaciones->get($exKey)?->first();
                                $puedeExtraordinario = $final && $final->calificacion < 7.0;
                                $calFinal = $ex ? $ex->calificacion : $final?->calificacion;
                                $aprobado = $calFinal !== null && $calFinal >= 7.0;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-100" x-data="{ aclarando: false }">
                                <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                    {{ $alumno->nombre_completo }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500 whitespace-nowrap">
                                    {{ $alumno->matricula }}
                                </td>

                                {{-- Final --}}
                                <td class="px-6 py-3 text-center">
                                    <input type="number"
                                           name="grades[{{ $alumno->id }}][final]"
                                           step="0.1" min="0" max="10"
                                           value="{{ $final?->calificacion }}"
                                           placeholder="—"
                                           @disabled(!$puedeCapturar)
                                           class="w-20 text-center text-sm border rounded-lg px-2 py-1.5 focus:outline-none transition-colors disabled:opacity-60
                                                  {{ $final ? ($final->calificacion >= 7.0 ? 'border-green-400 bg-green-50 text-green-700' : 'border-red-400 bg-red-50 text-red-700') : 'border-gray-300 bg-white text-gray-700' }}"
                                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                                           onblur="this.style.boxShadow=''">
                                </td>

                                {{-- Extraordinario --}}
                                <td class="px-6 py-3 text-center">
                                    @if ($puedeExtraordinario)
                                        <input type="number"
                                               name="grades[{{ $alumno->id }}][extraordinario]"
                                               step="0.1" min="0" max="10"
                                               value="{{ $ex?->calificacion }}"
                                               placeholder="—"
                                               @disabled(!$puedeCapturar)
                                               class="w-20 text-center text-sm border rounded-lg px-2 py-1.5 focus:outline-none transition-colors disabled:opacity-60
                                                      {{ $ex ? ($ex->calificacion >= 7.0 ? 'border-green-400 bg-green-50 text-green-700' : 'border-red-400 bg-red-50 text-red-700') : 'border-gray-300 bg-white text-gray-700' }}"
                                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                                               onblur="this.style.boxShadow=''">
                                    @else
                                        <span class="text-xs text-gray-300 italic">Requiere final reprobatorio</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if ($calFinal !== null)
                                        @if ($aprobado)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                                  style="background-color: #0F4229;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                                Aprobado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                                  style="background-color: #dc2626;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                                Reprobado
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                              style="background-color: #EFAD5A;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                            Pendiente
                                        </span>
                                    @endif
                                </td>

                                {{-- Aclaración --}}
                                @if (!$puedeCapturar)
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if ($final)
                                        <button type="button" @click="aclarando = !aclarando"
                                                class="text-xs font-bold px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700">
                                            Solicitar aclaración
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @if (!$puedeCapturar)
                            <tr x-show="aclarando" x-cloak>
                                <td colspan="6" class="px-6 py-4 bg-amber-50">
                                    <form method="POST"
                                          action="{{ route('profesor.aclaraciones.solicitar', [$carga->id, $alumno->id]) }}"
                                          class="flex flex-wrap items-end gap-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo</label>
                                            <select name="tipo" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs">
                                                <option value="final">Final</option>
                                                <option value="extraordinario" {{ $ex ? 'selected' : '' }}>Extraordinario</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Calificación correcta</label>
                                            <input type="number" name="calificacion_propuesta" step="0.1" min="0" max="10" required
                                                   class="w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-xs">
                                        </div>
                                        <div class="flex-1 min-w-[200px]">
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Motivo</label>
                                            <input type="text" name="motivo" required maxlength="500"
                                                   class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs">
                                        </div>
                                        <button type="submit"
                                                class="px-4 py-2 rounded-lg text-xs font-bold text-white"
                                                style="background-color: #0F4229;">
                                            Enviar a control escolar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Botones: Guardar borrador + Enviar a revisión --}}
            @if ($puedeCapturar)
            @php $tieneBorrador = $calificaciones->isNotEmpty(); @endphp
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3">

                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold border-2 transition-colors"
                        style="border-color: #0F4229; color: #0F4229; background: transparent;"
                        onmouseover="this.style.backgroundColor='#f0f9f4'"
                        onmouseout="this.style.backgroundColor='transparent'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Guardar borrador
                </button>

                @if ($tieneBorrador && $carga->estado_revision !== 'aprobado')
                <button type="button"
                        @click="confirmarEnvio = true"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Enviar a revisión
                </button>
                @endif

            </div>
            @endif

        </form>
        @endif

    </div>
</section>

{{-- Modal confirmación de envío --}}
<div x-show="confirmarEnvio"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="confirmarEnvio = false"
     class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/40 backdrop-blur-sm"
     style="display: none;">

    <div x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-2xl shadow-xl w-full max-w-md"
         @click.stop>

        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>

        <div class="px-6 py-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                     style="background-color: #f0f9f4;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">¿Enviar a control escolar?</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $carga->materia->nombre }} — Grupo {{ $carga->grupo->clave }}</p>
                </div>
            </div>

            <p class="text-sm text-gray-600 mb-6">
                Una vez enviadas, no podrás modificar las calificaciones hasta que control escolar las revise.
                Si detectan algún error, te notificarán y podrás volver a capturarlas.
            </p>

            <div class="flex items-center justify-end gap-3">
                <button type="button" @click="confirmarEnvio = false"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100
                               hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>

                <form method="POST" action="{{ route('profesor.calificaciones.enviar', $carga->id) }}">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        Confirmar envío
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
