@extends('layouts.app')

@section('title', 'Alumnos | UICM')

@section('content')

@php
$gruposJson = $grupos->map(fn($g) => [
    'id'          => $g->id,
    'clave'       => $g->clave,
    'periodo'     => $g->periodo->nombre ?? '',
    'cuatrimestre'=> $g->cuatrimestre,
    'programa_id' => $g->programa_id,
])->values()->toJson();
@endphp

<script>window.__gruposAlumnos = {!! $gruposJson !!};</script>

<section
    x-data="{
        showModal: false,
        editando: null,
        form: { estado: '', cuatrimestre_actual: '', password: '' },
        grupos: window.__gruposAlumnos || [],
        gruposFiltrados() {
            if (!this.editando) return [];
            return this.grupos.filter(g => g.programa_id == this.editando.programa_id);
        },
        abrirEditar(a) {
            this.editando = a;
            this.form = { estado: a.estado, cuatrimestre_actual: a.cuatrimestre_actual, grupo_id: a.grupo_id, password: '' };
            this.showModal = true;
        }
    }"
    class="bg-uicm-gray min-h-screen py-12 px-4">

    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Control Escolar
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Alumnos registrados</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Contadores (también filtran por estado) --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('admin.alumnos.index', array_filter(['q' => request('q'), 'programa' => request('programa')])) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150 block"
               style="border-color: #0F4229; {{ !request('estado') ? 'box-shadow: 0 0 0 2px #0F4229;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total de alumnos</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $conteo['total'] }}</p>
            </a>
            <a href="{{ route('admin.alumnos.index', array_filter(['q' => request('q'), 'programa' => request('programa'), 'estado' => 'activo'])) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150 block"
               style="border-color: #D4AF37; {{ request('estado') === 'activo' ? 'box-shadow: 0 0 0 2px #D4AF37;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Activos</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">{{ $conteo['activos'] }}</p>
            </a>
            <a href="{{ route('admin.alumnos.index', array_filter(['q' => request('q'), 'programa' => request('programa'), 'estado' => 'inactivo'])) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150 block"
               style="border-color: #9ca3af; {{ request('estado') === 'inactivo' ? 'box-shadow: 0 0 0 2px #9ca3af;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Inactivos</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-400">{{ $conteo['inactivos'] }}</p>
            </a>
            <a href="{{ route('admin.alumnos.index', array_filter(['q' => request('q'), 'programa' => request('programa'), 'estado' => 'baja'])) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150 block"
               style="border-color: #EF4444; {{ request('estado') === 'baja' ? 'box-shadow: 0 0 0 2px #EF4444;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Baja</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EF4444;">{{ $conteo['baja'] }}</p>
            </a>
        </div>

        {{-- Búsqueda y filtros --}}
        <form method="GET" action="{{ route('admin.alumnos.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
            <input type="hidden" name="estado" value="{{ request('estado') }}">

            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Buscar por nombre, matrícula o correo…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>

            <div class="relative sm:w-60">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <select name="programa"
                        onchange="this.form.submit()"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los programas</option>
                    @foreach($programas as $programa)
                        <option value="{{ $programa->id }}" @selected(request('programa') == $programa->id)>{{ $programa->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-150"
                    style="background-color: #0F4229;"
                    onmouseover="this.style.backgroundColor='#0a2e1c'"
                    onmouseout="this.style.backgroundColor='#0F4229'">
                Buscar
            </button>

            @if(request('q') || request('programa') || request('estado'))
            <a href="{{ route('admin.alumnos.index') }}"
               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-100 transition-colors duration-150">
                Limpiar
            </a>
            @endif
        </form>

        {{-- Tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col h-[350px]">
            <div class="h-1.5 w-full flex-shrink-0" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <h2 class="text-sm font-semibold text-gray-700">Listado de alumnos</h2>
                <span class="text-xs text-gray-400">{{ $alumnos->total() }} registros</span>
            </div>

            <div class="overflow-auto flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Alumno</th>
                            <th class="px-6 py-3">Matrícula</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3 text-center">Cuatrimestre</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($alumnos as $alumno)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                         style="background-color: #0F4229;">
                                        {{ strtoupper(substr($alumno->nombre, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $alumno->nombre_completo }}</p>
                                        <p class="text-xs text-gray-400">{{ $alumno->user?->email ?? $alumno->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-mono text-gray-700 text-xs">
                                {{ $alumno->matricula }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $alumno->programa->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-center text-gray-700">
                                {{ $alumno->cuatrimestre_actual ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $colorEstado = match($alumno->estado) {
                                        'activo'   => '#0F4229',
                                        'inactivo' => '#EFAD5A',
                                        'baja'     => '#dc2626',
                                        default    => '#9ca3af',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white capitalize"
                                      style="background-color: {{ $colorEstado }};">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                    {{ $alumno->estado }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <button type="button"
                                        @click="abrirEditar({
                                            id: {{ $alumno->id }},
                                            nombre: '{{ addslashes($alumno->nombre_completo) }}',
                                            matricula: '{{ $alumno->matricula }}',
                                            estado: '{{ $alumno->estado }}',
                                            cuatrimestre_actual: {{ $alumno->cuatrimestre_actual ?? 1 }},
                                            grupo_id: {{ $alumno->grupo_id ?? 'null' }},
                                            programa_id: {{ $alumno->programa_id ?? 'null' }},
                                            tiene_usuario: {{ $alumno->user_id ? 'true' : 'false' }}
                                        })"
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
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                                {{ request('q') || request('programa') || request('estado') ? 'No se encontraron alumnos con ese criterio.' : 'No hay alumnos registrados.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($alumnos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex-shrink-0">
                {{ $alumnos->links() }}
            </div>
            @endif
        </div>

    </div>

    {{-- ═══ MODAL EDITAR ALUMNO ═══ --}}
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
             class="bg-white rounded-2xl shadow-xl w-full max-w-md my-auto">

            <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>
            {{-- Cabecera --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold" style="color: #0F4229;">Editar alumno</h2>
                    <p class="text-xs text-gray-400 mt-0.5" x-text="editando ? editando.nombre + ' · ' + editando.matricula : ''"></p>
                </div>
                <button type="button" @click="showModal = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST"
                  :action="editando ? '/admin/alumnos/' + editando.id : '#'"
                  class="px-6 py-5 space-y-4">
                @csrf
                @method('PATCH')

                {{-- Estado --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Estado <span class="text-red-400">*</span>
                    </label>
                    <select name="estado" x-model="form.estado"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>

                {{-- Cuatrimestre --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Cuatrimestre actual <span class="text-red-400">*</span>
                    </label>
                    <input type="number" name="cuatrimestre_actual" x-model="form.cuatrimestre_actual"
                           min="1" max="12" placeholder="1"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <p class="mt-1 text-xs text-gray-400">Del 1 al 12.</p>
                </div>

                {{-- Grupo --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Grupo
                        <span class="text-gray-400 font-normal normal-case">(del programa del alumno)</span>
                    </label>
                    <select name="grupo_id" x-model="form.grupo_id"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">Sin grupo asignado</option>
                        <template x-for="g in gruposFiltrados()" :key="g.id">
                            <option :value="g.id" x-text="g.clave + ' · Cuatrimestre ' + g.cuatrimestre + ' · ' + g.periodo"></option>
                        </template>
                    </select>
                    <p x-show="gruposFiltrados().length === 0"
                       class="mt-1 text-xs text-gray-400 italic">
                        No hay grupos registrados para el programa de este alumno.
                    </p>
                </div>

                {{-- Contraseña — solo si tiene usuario vinculado --}}
                <div x-show="editando && editando.tiene_usuario">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Nueva contraseña
                        <span class="text-gray-400 font-normal normal-case">(dejar vacío para no cambiar)</span>
                    </label>
                    <div class="relative" x-data="{ showPwd: false }">
                        <input :type="showPwd ? 'text' : 'password'" name="password" x-model="form.password"
                               placeholder="Mínimo 6 caracteres"
                               minlength="6" maxlength="50"
                               class="w-full px-3 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:outline-none"
                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <button type="button" @click="showPwd = !showPwd"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div x-show="editando && !editando.tiene_usuario">
                    <p class="text-xs text-gray-400 italic">Este alumno aún no tiene credenciales de acceso al sistema.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-bold text-white rounded-lg transition-colors duration-200 shadow-sm"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>

@endsection
