@extends('layouts.app')

@section('title', 'Grupos | UICM')

@section('content')

<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6 min-h-screen"
     x-data="{
         busqueda: '',
         filtroPeriodo: '',
         filtroPrograma: '',
         filtroCuatrimestre: '',
     }">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Grupos</h1>
            <p class="text-sm text-gray-400 mt-0.5">Gestión de grupos por periodo, programa y cuatrimestre.</p>
        </div>
        <button onclick="document.getElementById('modal-crear').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-150"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2f1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo grupo
        </button>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Cards resumen --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4" style="border-color: #0F4229;">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total grupos</p>
            <p class="text-3xl font-extrabold" style="color: #0F4229;">{{ $grupos->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-emerald-400">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">En periodo activo</p>
            @php $periodoActivo = $periodos->where('estado','activo')->first(); @endphp
            <p class="text-3xl font-extrabold text-emerald-600">
                {{ $periodoActivo ? $grupos->where('periodo_id', $periodoActivo->id)->count() : 0 }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-400">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total alumnos</p>
            <p class="text-3xl font-extrabold text-yellow-600">{{ $grupos->sum('alumnos_count') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-400">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Programas</p>
            <p class="text-3xl font-extrabold text-blue-600">{{ $grupos->pluck('programa_id')->unique()->count() }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <h2 class="text-sm font-semibold text-gray-700">Filtros</h2>
        </div>
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-4 gap-4">
            <input type="text" x-model="busqueda" placeholder="Buscar por clave..."
                   class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none transition-all duration-150"
                   onfocus="this.style.borderColor='#0F4229'" onblur="this.style.borderColor='#e5e7eb'">

            <select x-model="filtroPeriodo"
                    class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none transition-all duration-150"
                    onfocus="this.style.borderColor='#0F4229'" onblur="this.style.borderColor='#e5e7eb'">
                <option value="">Todos los periodos</option>
                @foreach($periodos as $p)
                    <option value="{{ $p->id }}">{{ $p->label }}</option>
                @endforeach
            </select>

            <select x-model="filtroPrograma"
                    class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none transition-all duration-150"
                    onfocus="this.style.borderColor='#0F4229'" onblur="this.style.borderColor='#e5e7eb'">
                <option value="">Todos los programas</option>
                @foreach($programas as $p)
                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                @endforeach
            </select>

            <select x-model="filtroCuatrimestre"
                    class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none transition-all duration-150"
                    onfocus="this.style.borderColor='#0F4229'" onblur="this.style.borderColor='#e5e7eb'">
                <option value="">Todos los cuatrimestres</option>
                @foreach(range(1,12) as $n)
                    <option value="{{ $n }}">{{ $n }}°</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-6 py-3 text-left font-semibold">Clave</th>
                        <th class="px-6 py-3 text-left font-semibold">Programa</th>
                        <th class="px-6 py-3 text-left font-semibold">Periodo</th>
                        <th class="px-6 py-3 text-center font-semibold">Cuatrimestre</th>
                        <th class="px-6 py-3 text-center font-semibold">Alumnos / Cap.</th>
                        <th class="px-6 py-3 text-right font-semibold">Acciones</th>
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
                        <td class="px-6 py-4 font-mono font-bold text-gray-800">{{ $grupo->clave }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $grupo->programa->nombre }}</td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600">{{ $grupo->periodo->label ?? '—' }}</span>
                            @if($grupo->periodo?->estado === 'activo')
                                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-700">activo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-gray-700">{{ $grupo->cuatrimestre }}°</td>
                        <td class="px-6 py-4 text-center">
                            @php $pct = $grupo->capacidad > 0 ? round(($grupo->alumnos_count / $grupo->capacidad) * 100) : 0; @endphp
                            <span class="{{ $pct >= 90 ? 'text-red-600' : ($pct >= 70 ? 'text-yellow-600' : 'text-gray-700') }} font-semibold">
                                {{ $grupo->alumnos_count }}
                            </span>
                            <span class="text-gray-400"> / {{ $grupo->capacidad }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="abrirEditar({{ $grupo->id }}, '{{ addslashes($grupo->clave) }}', {{ $grupo->programa_id }}, {{ $grupo->periodo_id }}, {{ $grupo->cuatrimestre }}, {{ $grupo->capacidad }})"
                                        class="text-xs font-medium px-3 py-1.5 rounded-lg border transition-colors duration-150"
                                        style="border-color: #0F4229; color: #0F4229;"
                                        onmouseover="this.style.backgroundColor='#0F4229'; this.style.color='white'"
                                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#0F4229'">
                                    Editar
                                </button>
                                @if($grupo->alumnos_count === 0)
                                <form method="POST" action="{{ route('admin.grupos.destroy', $grupo->id) }}"
                                      onsubmit="return confirm('¿Eliminar el grupo {{ $grupo->clave }}? Esta acción no se puede deshacer.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs font-medium px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors duration-150">
                                        Eliminar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                            No hay grupos registrados. Crea el primero.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Modal crear --}}
<div id="modal-crear" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
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
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
                       required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Programa <span class="text-red-500">*</span></label>
                    <select name="programa_id"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
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
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
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
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
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
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
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
                        onmouseover="this.style.backgroundColor='#0a2f1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar --}}
<div id="modal-editar" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
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
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
                       required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Programa <span class="text-red-500">*</span></label>
                    <select id="edit-programa" name="programa_id"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
                            required>
                        @foreach($programas as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periodo <span class="text-red-500">*</span></label>
                    <select id="edit-periodo" name="periodo_id"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
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
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
                            required>
                        @foreach(range(1,12) as $n)
                            <option value="{{ $n }}">{{ $n }}°</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad <span class="text-red-500">*</span></label>
                    <input type="number" id="edit-capacidad" name="capacidad" min="1" max="100"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
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
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2f1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
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
