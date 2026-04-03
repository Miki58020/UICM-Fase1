@extends('layouts.app')

@section('title', 'Periodos académicos | UICM')

@section('content')

<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6 min-h-screen">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Periodos académicos</h1>
            <p class="text-sm text-gray-400 mt-0.5">Gestión de ciclos escolares y su estado.</p>
        </div>
        <button onclick="document.getElementById('modal-crear').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-150"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2f1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo periodo
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
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total</p>
            <p class="text-3xl font-extrabold" style="color: #0F4229;">{{ $periodos->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-emerald-400">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Activo</p>
            <p class="text-3xl font-extrabold text-emerald-600">{{ $periodos->where('estado','activo')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-400">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Próximos</p>
            <p class="text-3xl font-extrabold text-blue-600">{{ $periodos->where('estado','proximo')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-gray-300">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Cerrados</p>
            <p class="text-3xl font-extrabold text-gray-500">{{ $periodos->where('estado','cerrado')->count() }}</p>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h2 class="text-sm font-semibold text-gray-700">Listado de periodos</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-6 py-3 text-left font-semibold">Clave</th>
                        <th class="px-6 py-3 text-left font-semibold">Nombre completo</th>
                        <th class="px-6 py-3 text-left font-semibold">Inicio</th>
                        <th class="px-6 py-3 text-left font-semibold">Fin</th>
                        <th class="px-6 py-3 text-center font-semibold">Grupos</th>
                        <th class="px-6 py-3 text-center font-semibold">Estado</th>
                        <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($periodos as $periodo)
                    <tr class="hover:bg-gray-50 transition-colors duration-100">
                        <td class="px-6 py-4 font-mono font-bold text-gray-800">{{ $periodo->nombre }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $periodo->label }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $periodo->fecha_inicio->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $periodo->fecha_fin->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold"
                                  style="background-color: #f0f9f4; color: #0F4229;">
                                {{ $periodo->grupos_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($periodo->estado === 'activo')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                </span>
                            @elseif($periodo->estado === 'proximo')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Próximo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Cerrado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Editar --}}
                                <button onclick="abrirEditar({{ $periodo->id }}, '{{ $periodo->nombre }}', '{{ addslashes($periodo->label) }}', '{{ $periodo->fecha_inicio->format('Y-m-d') }}', '{{ $periodo->fecha_fin->format('Y-m-d') }}')"
                                        class="text-xs font-medium px-3 py-1.5 rounded-lg border transition-colors duration-150"
                                        style="border-color: #0F4229; color: #0F4229;"
                                        onmouseover="this.style.backgroundColor='#0F4229'; this.style.color='white'"
                                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#0F4229'">
                                    Editar
                                </button>

                                {{-- Cambiar estado --}}
                                @if($periodo->estado !== 'activo')
                                <form method="POST" action="{{ route('admin.periodos.activar', $periodo->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="text-xs font-medium px-3 py-1.5 rounded-lg border border-emerald-400 text-emerald-600 hover:bg-emerald-50 transition-colors duration-150">
                                        Activar
                                    </button>
                                </form>
                                @endif

                                @if($periodo->estado === 'activo')
                                <form method="POST" action="{{ route('admin.periodos.cerrar', $periodo->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50 transition-colors duration-150">
                                        Cerrar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">
                            No hay periodos registrados. Crea el primero.
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
            <h3 class="font-bold text-gray-800">Nuevo periodo</h3>
            <button onclick="document.getElementById('modal-crear').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.periodos.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Clave <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" placeholder="ej: 2026-2"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
                       required>
                <p class="text-xs text-gray-400 mt-1">Formato recomendado: año-número (ej: 2026-2)</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                <input type="text" name="label" placeholder="ej: Segundo Cuatrimestre 2026"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
                       required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha_inicio"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha_fin"
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
            <h3 class="font-bold text-gray-800">Editar periodo</h3>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Clave <span class="text-red-500">*</span></label>
                <input type="text" id="edit-nombre" name="nombre"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
                       required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                <input type="text" id="edit-label" name="label"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
                       required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio <span class="text-red-500">*</span></label>
                    <input type="date" id="edit-fecha-inicio" name="fecha_inicio"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-green-200"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin <span class="text-red-500">*</span></label>
                    <input type="date" id="edit-fecha-fin" name="fecha_fin"
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
function abrirEditar(id, nombre, label, fechaInicio, fechaFin) {
    document.getElementById('form-editar').action = '/admin/periodos/' + id;
    document.getElementById('edit-nombre').value = nombre;
    document.getElementById('edit-label').value = label;
    document.getElementById('edit-fecha-inicio').value = fechaInicio;
    document.getElementById('edit-fecha-fin').value = fechaFin;
    document.getElementById('modal-editar').classList.remove('hidden');
}
</script>
@endpush
