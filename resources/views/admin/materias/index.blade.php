@extends('layouts.app')

@section('title', 'Gestión de Materias | UICM')

@section('content')

<section
    x-data="{
        showModal: {{ old('_materia_form') ? 'true' : 'false' }},
        editando: {{ old('_materia_form') && old('_method') === 'PATCH' ? 'true' : 'null' }},
        form: {
            clave:       '{{ addslashes(old('clave', '')) }}',
            nombre:      '{{ addslashes(old('nombre', '')) }}',
            programa_id: '{{ old('programa_id', '') }}',
            cuatrimestre:'{{ old('cuatrimestre', '') }}',
            creditos:    '{{ old('creditos', '') }}'
        },
        archivoCsv: '',
        programasDuracion: @js($programas->pluck('duracion_cuatrimestres', 'id')),
        cuatrimestresDisponibles() {
            const max = this.programasDuracion[this.form.programa_id] || 12;
            return Array.from({ length: max }, (_, i) => i + 1);
        },
        onProgramaChange() {
            const max = this.programasDuracion[this.form.programa_id] || 12;
            if (this.form.cuatrimestre && Number(this.form.cuatrimestre) > max) this.form.cuatrimestre = '';
        },
        abrir() { this.editando = null; this.form = { clave: '', nombre: '', programa_id: '', cuatrimestre: '', creditos: '' }; this.showModal = true; },
        abrirEditar(m) { this.editando = m; this.form = { clave: m.clave, nombre: m.nombre, programa_id: m.programa_id, cuatrimestre: m.cuatrimestre, creditos: m.creditos }; this.showModal = true; }
    }"
    class="bg-uicm-gray min-h-screen py-12 px-4">

    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado + botón --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                    Coordinación Académica
                </p>
                <h1 class="text-2xl font-extrabold text-gray-900">Gestión de Materias</h1>
                <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
            </div>
            <button type="button" @click="abrir()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold
                       text-white shadow-sm transition-colors duration-200 self-start sm:self-auto"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2e1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva materia
            </button>
        </div>

        {{-- Contadores --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
            <a href="{{ route('admin.materias.index', array_filter(['q' => request('q'), 'programa' => request('programa')])) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 block" style="border-color: #0F4229; {{ request('activo') === null ? 'box-shadow: 0 0 0 2px #0F4229;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total registradas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $conteo['total'] }}</p>
            </a>
            <a href="{{ route('admin.materias.index', array_filter(['q' => request('q'), 'programa' => request('programa'), 'activo' => '1'], fn($v) => $v !== null && $v !== '')) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 block" style="border-color: #D4AF37; {{ request('activo') === '1' ? 'box-shadow: 0 0 0 2px #D4AF37;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Activas</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">
                    {{ $conteo['activas'] }}
                </p>
            </a>
            <a href="{{ route('admin.materias.index', array_filter(['q' => request('q'), 'programa' => request('programa'), 'activo' => '0'], fn($v) => $v !== null && $v !== '')) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 col-span-2 sm:col-span-1 block" style="border-color: #9ca3af; {{ request('activo') === '0' ? 'box-shadow: 0 0 0 2px #9ca3af;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Inactivas</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">
                    {{ $conteo['inactivas'] }}
                </p>
            </a>
        </div>

        {{-- Búsqueda y filtros --}}
        <form method="GET" action="{{ route('admin.materias.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
            <input type="hidden" name="activo" value="{{ request('activo') }}">

            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Buscar por clave o nombre…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div class="relative sm:w-52">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <select name="programa" onchange="this.form.submit()"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los programas</option>
                    @foreach ($programas as $prog)
                        <option value="{{ $prog->id }}" @selected(request('programa') == $prog->id)>{{ $prog->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        {{-- Carga masiva de materias (CSV) --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-8">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: #0F4229;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3 3-3m-3-7v9"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700">Carga masiva de materias (CSV)</h2>
            </div>

            <div class="px-6 py-5">

                {{-- Guía rápida --}}
                <div class="bg-uicm-gray rounded-xl p-4 mb-5 text-sm text-gray-600">
                    <p class="font-semibold text-gray-800 mb-2">¿Cómo funciona?</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Descarga la <strong>plantilla CSV</strong> y llena un renglón por cada materia: <strong>clave</strong>, <strong>nombre</strong>, <strong>programa_clave</strong>, <strong>cuatrimestre</strong>, <strong>creditos</strong>.</li>
                        <li>Un mismo archivo puede incluir materias de <strong>varios programas a la vez</strong>.</li>
                        <li>Si la <strong>clave</strong> ya existe, se <strong>actualiza</strong> esa materia (nombre, programa, cuatrimestre, créditos); si no existe, se <strong>crea</strong> nueva — puedes volver a subir el mismo archivo corregido sin duplicar nada.</li>
                        <li><strong>programa_clave</strong> es la clave interna del programa (ej. "administracion", "lengua_inglesa"), no el nombre completo. <strong>cuatrimestre</strong> no puede ser mayor a la duración del programa.</li>
                    </ol>
                </div>

                <form method="POST" action="{{ route('admin.materias.importar') }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('admin.materias.plantilla') }}"
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
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-150 whitespace-nowrap"
                                style="background-color: #D4AF37;"
                                onmouseover="this.style.backgroundColor='#b9952c'"
                                onmouseout="this.style.backgroundColor='#D4AF37'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 7.5L12 3 7.5 7.5M12 3v13.5"/>
                            </svg>
                            Subir e importar
                        </button>
                    </div>
                </form>

            </div>
        </div>

        {{-- Card tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Catálogo de materias</h2>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green">{{ $materias->total() }} registros</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Clave</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3 text-center">Cuatrimestre</th>
                            <th class="px-6 py-3 text-center">Créditos</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($materias as $m)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">

                            <td class="px-6 py-4 font-mono text-xs font-bold whitespace-nowrap" style="color: #0F4229;">
                                {{ $m->clave }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $m->nombre }}</td>
                            <td class="px-6 py-4 text-gray-600 text-xs">{{ $m->programa->nombre ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white"
                                      style="background-color: #D4AF37;">
                                    {{ $m->cuatrimestre }}°
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-gray-700">{{ $m->creditos }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($m->activo)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                        Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Editar --}}
                                    <button type="button"
                                            @click="abrirEditar({ id: {{ $m->id }}, clave: '{{ $m->clave }}', nombre: '{{ addslashes($m->nombre) }}', programa_id: '{{ $m->programa_id }}', cuatrimestre: '{{ $m->cuatrimestre }}', creditos: '{{ $m->creditos }}' })"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                            style="background-color: #D4AF37;"
                                            onmouseover="this.style.backgroundColor='#b8962e'"
                                            onmouseout="this.style.backgroundColor='#D4AF37'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                     m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </button>

                                    {{-- Toggle activo/inactivo --}}
                                    <form method="POST" action="{{ route('admin.materias.toggle', $m->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        @if ($m->activo)
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-gray-400 hover:bg-gray-500 transition-colors duration-150">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Desactivar
                                            </button>
                                        @else
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                                    style="background-color: #0F4229;"
                                                    onmouseover="this.style.backgroundColor='#0a2e1c'"
                                                    onmouseout="this.style.backgroundColor='#0F4229'">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Activar
                                            </button>
                                        @endif
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                                    <p class="text-sm text-gray-500 max-w-xs mx-auto">{{ request('q') || request('programa') || request('activo') !== null ? 'No se encontraron materias con ese criterio.' : 'No hay materias registradas.' }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($materias->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex-shrink-0">
                {{ $materias->links() }}
            </div>
            @endif
        </div>


    </div>

    {{-- MODAL crear / editar --}}
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto bg-black/50 backdrop-blur-sm"
         @keydown.escape.window="showModal = false"
         x-cloak>

        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg my-auto">

            <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold" style="color: #0F4229;">
                        <span x-text="editando ? 'Editar materia' : 'Nueva materia'"></span>
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Completa los campos del formulario</p>
                </div>
                <button type="button" @click="showModal = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Formulario dinámico: POST (crear) o PATCH (editar) --}}
            <form method="POST"
                  :action="editando ? '/admin/materias/' + editando.id : '{{ route('admin.materias.store') }}'"
                  class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" x-bind:value="editando ? 'PATCH' : 'POST'">
                <input type="hidden" name="_materia_form" value="1">

                {{-- Clave + Créditos --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                            Clave <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="clave" x-model="form.clave"
                               placeholder="Ej. IS-101"
                               maxlength="6"
                               oninput="formatClaveMateria(this); this.$el.dispatchEvent(new Event('input'))"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <p class="mt-1 text-xs text-gray-400">2 letras + 3 dígitos (Ej. IS-101).</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                            Créditos <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="creditos" x-model="form.creditos"
                               min="1" max="20" placeholder="8"
                               oninput="if (this.value !== '' && Number(this.value) > 20) this.value = 20; if (this.value !== '' && Number(this.value) < 1) this.value = 1;"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none"
                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <p class="mt-1 text-xs text-gray-400">Entre 1 y 20 créditos.</p>
                    </div>
                </div>

                {{-- Nombre --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Nombre de la materia <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="nombre" x-model="form.nombre"
                           placeholder="Ej. Programación I"
                           maxlength="100"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>

                {{-- Programa --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Programa académico <span class="text-red-400">*</span>
                    </label>
                    @php $nivelesLabel = ['licenciatura' => 'Licenciaturas', 'maestria' => 'Maestrías', 'doctorado' => 'Doctorado']; @endphp
                    <select name="programa_id" x-model="form.programa_id" @change="onProgramaChange()"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">Selecciona un programa</option>
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

                {{-- Cuatrimestre --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Cuatrimestre <span class="text-red-400">*</span>
                    </label>
                    <select name="cuatrimestre" x-model="form.cuatrimestre"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">Selecciona el cuatrimestre</option>
                        <template x-for="n in cuatrimestresDisponibles()" :key="n">
                            <option :value="n" x-text="n + '°'"></option>
                        </template>
                    </select>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-bold text-white rounded-lg transition-colors duration-200 shadow-sm"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span x-text="editando ? 'Guardar cambios' : 'Guardar materia'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

</section>

@push('scripts')
<script>
function formatClaveMateria(el) {
    var raw = el.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    var s1 = raw.match(/^[A-Z]{0,2}/)[0];
    var result = s1;
    if (s1.length < 2) { el.value = result; return; }
    var rem = raw.slice(2);
    var s2 = rem.match(/^[0-9]{0,3}/)[0];
    if (s2.length > 0) result += '-' + s2;
    el.value = result;
}
</script>
@endpush

@endsection
