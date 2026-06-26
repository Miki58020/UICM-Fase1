@extends('layouts.app')

@section('title', 'Mis materias | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{ modalContrasena: {{ $errors->has('password') ? 'true' : 'false' }} }">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        <div class="mb-8 flex items-start justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Profesor</p>
                <h1 class="text-2xl font-extrabold text-gray-900">Mis materias asignadas</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $profesor->nombre }}</p>
                <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
            </div>
            <button type="button" @click="modalContrasena = true"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border-2 transition-colors"
                    style="border-color: #0F4229; color: #0F4229;"
                    onmouseover="this.style.backgroundColor='#f0f9f4'"
                    onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                             a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                Cambiar contraseña
            </button>
        </div>

        @if ($ilustrativo)
            <x-banner-desarrollo>
                Este panel está en desarrollo. La información de materias, grupos y alumnos que se muestra
                a continuación es <strong>ilustrativa</strong>, únicamente para mostrar cómo se vería una vez
                que se asignen materias y grupos reales a los profesores.
            </x-banner-desarrollo>
        @endif

        {{-- Resumen / dashboard --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
            <div class="bg-white rounded-2xl shadow-md p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Materias</p>
                <p class="text-2xl font-extrabold" style="color: #0F4229;">{{ $stats['materias'] }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Grupos</p>
                <p class="text-2xl font-extrabold" style="color: #0F4229;">{{ $stats['grupos'] }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Alumnos</p>
                <p class="text-2xl font-extrabold" style="color: #0F4229;">{{ $stats['alumnos'] }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Por calificar</p>
                <p class="text-2xl font-extrabold" style="color: #D4AF37;">{{ $stats['pendientes'] }}</p>
            </div>
        </div>

        @if ($cargas->isEmpty())
            @if ($ilustrativo)
                {{-- Vista previa ilustrativa --}}
                @php
                    $filasEjemplo = [
                        ['grupo' => 'PSI-2026-3-A', 'materia' => 'Introducción a la Psicología Educativa', 'clave' => 'PSI-101', 'horario' => 'Lun y Mié 8:00 - 10:00', 'aula' => 'A-101'],
                        ['grupo' => 'MBA-2026-3-A', 'materia' => 'Dirección Estratégica',                  'clave' => 'MBA-101', 'horario' => 'Mar y Jue 16:00 - 18:00', 'aula' => 'B-203'],
                        ['grupo' => 'DOE-2026-3-A', 'materia' => 'Epistemología de la Investigación',       'clave' => 'DOE-101', 'horario' => 'Vie 9:00 - 13:00',          'aula' => 'C-305'],
                    ];
                @endphp
                <div class="mb-8">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">
                        Período: Cuatrimestre Junio-Septiembre 2026
                    </h2>
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-6 py-3">Grupo</th>
                                        <th class="px-6 py-3">Materia</th>
                                        <th class="px-6 py-3">Horario</th>
                                        <th class="px-6 py-3">Aula</th>
                                        <th class="px-6 py-3 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($filasEjemplo as $fila)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-700">
                                            {{ $fila['grupo'] }}
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-800">
                                            {{ $fila['materia'] }}
                                            <span class="block text-xs text-gray-400">{{ $fila['clave'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 text-xs">
                                            {{ $fila['horario'] }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 text-xs">
                                            {{ $fila['aula'] }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-gray-400 bg-gray-100 whitespace-nowrap">
                                                Solo ilustrativo
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-md p-12 text-center text-gray-400 text-sm">
                    No tienes materias asignadas por el momento.
                </div>
            @endif
        @else
            {{-- Agrupar por período --}}
            @foreach ($cargas->groupBy(fn($c) => $c->periodo->nombre ?? 'Sin período') as $periodoNombre => $cargasPeriodo)
            <div class="mb-8">
                <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">
                    Período: {{ $periodoNombre }}
                </h2>
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                    <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3">Grupo</th>
                                    <th class="px-6 py-3">Materia</th>
                                    <th class="px-6 py-3">Horario</th>
                                    <th class="px-6 py-3">Aula</th>
                                    <th class="px-6 py-3 text-center">Revisión</th>
                                    <th class="px-6 py-3 text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($cargasPeriodo as $carga)
                                @php
                                    $estadoBadge = match($carga->estado_revision) {
                                        'pendiente' => ['#EFAD5A', 'En revisión'],
                                        'aprobado'  => ['#0F4229', 'Aprobado'],
                                        'rechazado' => ['#dc2626', 'Rechazado'],
                                        default     => ['#9ca3af', 'Sin enviar'],
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-700">
                                        {{ $carga->grupo->clave ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $carga->materia->nombre ?? '—' }}
                                        <span class="block text-xs text-gray-400">{{ $carga->materia->clave ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">
                                        {{ $carga->horario ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">
                                        {{ $carga->aula ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                              style="background-color: {{ $estadoBadge[0] }};">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                            {{ $estadoBadge[1] }}
                                        </span>
                                        @if ($carga->estado_revision === 'rechazado' && $carga->motivo_rechazo)
                                            <span class="block text-xs text-red-500 mt-1 max-w-[160px]">{{ $carga->motivo_rechazo }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('profesor.calificaciones.capturar', $carga->id) }}"
                                           class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-white whitespace-nowrap"
                                           style="background-color: #0F4229;"
                                           onmouseover="this.style.backgroundColor='#0a2e1c'"
                                           onmouseout="this.style.backgroundColor='#0F4229'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                            </svg>
                                            Capturar calificaciones
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        @endif

    </div>

    {{-- Modal cambio de contraseña --}}
    <div x-show="modalContrasena"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display: none;">

        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                 a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <h2 class="text-sm font-bold text-gray-800">Cambiar contraseña</h2>
                </div>
                <button type="button" @click="modalContrasena = false"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('profesor.cambiar-password') }}" class="px-6 py-5 space-y-4"
                  x-data="{ showPwd: false }">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Nueva contraseña <span class="normal-case font-normal text-gray-400">(mín. 8 caracteres)</span>
                    </label>
                    <input :type="showPwd ? 'text' : 'password'" name="password" required
                           class="w-full px-4 py-2.5 text-sm border rounded-xl bg-white focus:outline-none
                                  @error('password') border-red-400 @else border-gray-300 @enderror"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                           onblur="this.style.borderColor=''; this.style.boxShadow=''">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Confirmar contraseña
                    </label>
                    <input :type="showPwd ? 'text' : 'password'" name="password_confirmation" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                           onblur="this.style.borderColor=''; this.style.boxShadow=''">
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button type="button" @click="showPwd = !showPwd"
                            class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 transition-colors select-none">
                        <svg x-show="!showPwd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPwd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                        </svg>
                        <span x-text="showPwd ? 'Ocultar contraseñas' : 'Mostrar contraseñas'"></span>
                    </button>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="modalContrasena = false"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold border-2"
                            style="border-color: #0F4229; color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#f0f9f4'"
                            onmouseout="this.style.backgroundColor='transparent'">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>
@endsection
