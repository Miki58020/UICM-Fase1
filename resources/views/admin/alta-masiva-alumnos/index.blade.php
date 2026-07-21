@extends('layouts.app')

@section('title', 'Alta Masiva de Alumnos | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Coordinación Académica
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Alta masiva de alumnos</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        <div class="space-y-6">

            {{-- Card de alta masiva --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3 3-3m-3-7v9"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Alumnos migrados por lote (CSV)</h2>
                </div>

                <div class="px-6 py-5">

                    {{-- Guía rápida --}}
                    <div class="bg-uicm-gray rounded-xl p-4 mb-5 text-sm text-gray-600">
                        <p class="font-semibold text-gray-800 mb-2">¿Cómo funciona?</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Elige el <strong>periodo</strong>, <strong>programa</strong> y <strong>grupo destino</strong> donde se incorporarán los alumnos (su cuatrimestre será el del grupo elegido).</li>
                            <li>Descarga la <strong>plantilla CSV</strong> y llena un renglón por cada alumno (nombre, apellidos, CURP, correo, etc.).</li>
                            <li>El <strong>correo</strong> que captures es el personal del alumno: ahí se enviarán su matrícula y contraseña. Después usará su correo institucional.</li>
                            <li>Sube el archivo. El sistema generará matrícula y usuario automáticamente; si el grupo se llena, crea grupos adicionales sin detener la carga. No se requiere pago de inscripción para estos alumnos.</li>
                        </ol>
                    </div>

                    @php $nivelesLabel = ['licenciatura' => 'Licenciaturas', 'maestria' => 'Maestrías', 'doctorado' => 'Doctorado']; @endphp

                    <form method="POST" action="{{ route('admin.alta-masiva-alumnos.importar') }}"
                          enctype="multipart/form-data"
                          @submit="enviando = true"
                          x-data="{
                              periodo_id: '',
                              programa_id: '',
                              enviando: false,
                              archivoCsv: '',
                              grupos: @js($grupos->map(fn($g) => ['id' => $g->id, 'clave' => $g->clave, 'periodo_id' => $g->periodo_id, 'programa_id' => $g->programa_id])),
                              get gruposFiltrados() {
                                  return this.grupos.filter(g =>
                                      (!this.periodo_id  || g.periodo_id  == this.periodo_id) &&
                                      (!this.programa_id || g.programa_id == this.programa_id)
                                  );
                              }
                          }">
                        @csrf

                        {{-- Selects de destino --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                    Periodo
                                </label>
                                <select x-model="periodo_id"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700 outline-none bg-white">
                                    <option value="">Todos</option>
                                    @foreach ($periodos as $p)
                                        <option value="{{ $p->id }}">{{ $p->label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                    Programa / Carrera
                                </label>
                                <select x-model="programa_id"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700 outline-none bg-white">
                                    <option value="">Todos</option>
                                    @foreach ($nivelesLabel as $nivel => $label)
                                        @if ($programas->where('nivel', $nivel)->isNotEmpty())
                                        <optgroup label="{{ $label }}">
                                            @foreach ($programas->where('nivel', $nivel) as $prog)
                                                <option value="{{ $prog->id }}">{{ $prog->nombre }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                    Grupo destino
                                </label>
                                <select name="grupo_id" required
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-700 outline-none bg-white">
                                    <option value="">— Selecciona un grupo —</option>
                                    <template x-for="g in gruposFiltrados" :key="g.id">
                                        <option :value="g.id" x-text="g.clave"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        {{-- Plantilla + carga --}}
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('admin.alta-masiva-alumnos.plantilla') }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-150 whitespace-nowrap"
                               style="background-color: #0F4229;"
                               onmouseover="this.style.backgroundColor='#0a2e1c'"
                               onmouseout="this.style.backgroundColor='#0F4229'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-3L12 16.5l4.5-3M12 16.5V3"/>
                                </svg>
                                Descargar plantilla CSV
                            </a>

                            <input type="file" name="csv" accept=".csv,text/csv" required
                                   @change="archivoCsv = $event.target.files[0]?.name || ''"
                                   :class="archivoCsv ? 'border-green-400 bg-green-50 text-green-700' : 'border-gray-200 text-gray-600'"
                                   class="flex-1 text-sm border-2 border-dashed rounded-xl px-4 py-2.5 outline-none transition-colors duration-150 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700">

                            <button type="submit"
                                    :disabled="enviando"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-150 whitespace-nowrap"
                                    style="background-color: #D4AF37;"
                                    onmouseover="if(!this.disabled) this.style.backgroundColor='#b9952c'"
                                    onmouseout="if(!this.disabled) this.style.backgroundColor='#D4AF37'">
                                <svg x-show="enviando" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"
                                     style="display:none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"></path>
                                </svg>
                                <svg x-show="!enviando" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 7.5L12 3 7.5 7.5M12 3v13.5"/>
                                </svg>
                                <span x-text="enviando ? 'Importando… puede tardar si hay que reintentar el envío de correos' : 'Subir e importar'"></span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>{{-- /space-y-6 --}}

    </div>
</section>

@endsection
