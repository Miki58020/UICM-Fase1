@extends('layouts.app')

@section('title', 'Grupos | UICM')

@section('content')

<section
    x-data="{
        busqueda: '',
        filtroPeriodo: '',
        filtroPrograma: '',
        filtroCuatrimestre: '',
    }"
    class="bg-uicm-gray min-h-screen py-12 px-4">

<div class="container mx-auto px-4 lg:px-12 max-w-7xl">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Control Escolar
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Grupos</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>
        <button onclick="document.getElementById('modal-crear').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200 self-start sm:self-auto"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2e1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo grupo
        </button>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-green-50" style="border-color: #0F4229;">
        <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-red-50" style="border-color: #ef4444;">
        <p class="text-sm font-semibold text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Cards resumen --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total grupos</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $grupos->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">En periodo activo</p>
            @php $periodoActivo = $periodos->where('estado','activo')->first(); @endphp
            <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">
                {{ $periodoActivo ? $grupos->where('periodo_id', $periodoActivo->id)->count() : 0 }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total alumnos</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $grupos->sum('alumnos_count') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #9ca3af;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Programas</p>
            <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $grupos->pluck('programa_id')->unique()->count() }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input type="text" x-model="busqueda" placeholder="Buscar por clave..."
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
            <select x-model="filtroPeriodo"
                    class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                <option value="">Todos los periodos</option>
                @foreach($periodos as $p)
                    <option value="{{ $p->id }}">{{ $p->label }}</option>
                @endforeach
            </select>
        </div>

        <div class="relative sm:w-52">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <select x-model="filtroPrograma"
                    class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                <option value="">Todos los programas</option>
                @foreach($programas as $p)
                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="relative sm:w-44">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <select x-model="filtroCuatrimestre"
                    class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                <option value="">Todos los cuatrimestres</option>
                @foreach(range(1,12) as $n)
                    <option value="{{ $n }}">{{ $n }}°</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Listado de grupos</h2>
            <span class="text-xs text-gray-400">{{ $grupos->count() }} registro{{ $grupos->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Clave</th>
                        <th class="px-6 py-3">Programa</th>
                        <th class="px-6 py-3">Periodo</th>
                        <th class="px-6 py-3 text-center">Cuatrimestre</th>
                        <th class="px-6 py-3 text-center">Alumnos / Cap.</th>
                        <th class="px-6 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($grupos as $grupo)
                    <tr class="hover:bg-gray-50 transition-colors duration-100"
                        x-show="
                            (!busqueda        || '{{ strtolower($grupo->clave) }}'.includes(busqueda.toLowerCase())) &&
                            (!filtroPeriodo   || '{{ $grupo->periodo_id }}' == filtroPeriodo) &&
                            (!filtroPrograma  || '{{ $grupo->programa_id }}' == filtroPrograma) &&
                            (!filtroCuatrimestre || '{{ $grupo->cuatrimestre }}' == filtroCuatrimestre)
                        ">
                        <td class="px-6 py-4 font-mono text-xs font-bold whitespace-nowrap" style="color: #0F4229;">
                            {{ $grupo->clave }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $grupo->programa->nombre }}</td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600 text-xs">{{ $grupo->periodo->label ?? '—' }}</span>
                            @if($grupo->periodo?->estado === 'activo')
                                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold text-white"
                                      style="background-color: #0F4229;">activo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white"
                                  style="background-color: #D4AF37;">
                                {{ $grupo->cuatrimestre }}°
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php $pct = $grupo->capacidad > 0 ? round(($grupo->alumnos_count / $grupo->capacidad) * 100) : 0; @endphp
                            <span class="{{ $pct >= 90 ? 'text-red-600' : ($pct >= 70 ? 'text-yellow-600' : 'text-gray-700') }} font-semibold">
                                {{ $grupo->alumnos_count }}
                            </span>
                            <span class="text-gray-400"> / {{ $grupo->capacidad }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Editar --}}
                                <button onclick="abrirEditar({{ $grupo->id }}, '{{ addslashes($grupo->clave) }}', {{ $grupo->programa_id }}, {{ $grupo->periodo_id }}, {{ $grupo->cuatrimestre }}, {{ $grupo->capacidad }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                        style="background-color: #D4AF37;"
                                        onmouseover="this.style.backgroundColor='#b8962e'"
                                        onmouseout="this.style.backgroundColor='#D4AF37'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </button>

                                {{-- Eliminar --}}
                                @if($grupo->alumnos_count === 0)
                                <form method="POST" action="{{ route('admin.grupos.destroy', $grupo->id) }}"
                                      onsubmit="return confirm('¿Eliminar el grupo {{ $grupo->clave }}? Esta acción no se puede deshacer.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border border-red-200 text-red-500 hover:bg-red-50 transition-colors duration-150">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Eliminar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                            No hay grupos registrados. Crea el primero.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</section>

{{-- Modal crear --}}
<div id="modal-crear" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Nuevo grupo</h3>
            <button onclick="document.getElementById('modal-crear').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.grupos.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Clave del grupo <span class="text-red-500">*</span></label>
                <input type="text" name="clave" placeholder="ej: PSI-2026-1-A"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                       required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Programa <span class="text-red-500">*</span></label>
                    <select name="programa_id"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                            required>
                        <option value="">Seleccionar</option>
                        @foreach($programas as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periodo <span class="text-red-500">*</span></label>
                    <select name="periodo_id"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                            required>
                        <option value="">Seleccionar</option>
                        @foreach($periodos as $p)
                            <option value="{{ $p->id }}" {{ $p->estado === 'activo' ? 'selected' : '' }}>
                                {{ $p->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cuatrimestre <span class="text-red-500">*</span></label>
                    <select name="cuatrimestre"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                            required>
                        <option value="">Seleccionar</option>
                        @foreach(range(1,12) as $n)
                            <option value="{{ $n }}">{{ $n }}°</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad <span class="text-red-500">*</span></label>
                    <input type="number" name="capacidad" value="30" min="1" max="100"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                           required>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-crear').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar --}}
<div id="modal-editar" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #D4AF37;"></div>
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Editar grupo</h3>
            <button onclick="document.getElementById('modal-editar').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-editar" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Clave del grupo <span class="text-red-500">*</span></label>
                <input type="text" id="edit-clave" name="clave"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                       required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Programa <span class="text-red-500">*</span></label>
                    <select id="edit-programa" name="programa_id"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                            required>
                        @foreach($programas as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periodo <span class="text-red-500">*</span></label>
                    <select id="edit-periodo" name="periodo_id"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                            required>
                        @foreach($periodos as $p)
                            <option value="{{ $p->id }}">{{ $p->label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cuatrimestre <span class="text-red-500">*</span></label>
                    <select id="edit-cuatrimestre" name="cuatrimestre"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                            required>
                        @foreach(range(1,12) as $n)
                            <option value="{{ $n }}">{{ $n }}°</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad <span class="text-red-500">*</span></label>
                    <input type="number" id="edit-capacidad" name="capacidad" min="1" max="100"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                           required>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-editar').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
                        style="background-color: #D4AF37;"
                        onmouseover="this.style.backgroundColor='#b8962e'"
                        onmouseout="this.style.backgroundColor='#D4AF37'">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirEditar(id, clave, programaId, periodoId, cuatrimestre, capacidad) {
    document.getElementById('form-editar').action = '/admin/grupos/' + id;
    document.getElementById('edit-clave').value = clave;
    document.getElementById('edit-programa').value = programaId;
    document.getElementById('edit-periodo').value = periodoId;
    document.getElementById('edit-cuatrimestre').value = cuatrimestre;
    document.getElementById('edit-capacidad').value = capacidad;
    document.getElementById('modal-editar').classList.remove('hidden');
}
</script>
@endpush
