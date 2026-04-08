@extends('layouts.app')

@section('title', 'Periodos académicos | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
<div class="container mx-auto px-4 lg:px-12 max-w-7xl">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Control Escolar
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Periodos académicos</h1>
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
            Nuevo periodo
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

    @if($errors->any())
    <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-red-50" style="border-color: #ef4444;">
        @foreach($errors->all() as $error)
            <p class="text-sm text-red-700">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- Cards resumen --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $periodos->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Activo</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">{{ $periodos->where('estado','activo')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Próximos</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $periodos->where('estado','proximo')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #9ca3af;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Cerrados</p>
            <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $periodos->where('estado','cerrado')->count() }}</p>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Listado de periodos</h2>
            <span class="text-xs text-gray-400">{{ $periodos->count() }} registro{{ $periodos->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Clave</th>
                        <th class="px-6 py-3">Nombre completo</th>
                        <th class="px-6 py-3">Inicio</th>
                        <th class="px-6 py-3">Fin</th>
                        <th class="px-6 py-3 text-center">Grupos</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($periodos as $periodo)
                    <tr class="hover:bg-gray-50 transition-colors duration-100">
                        <td class="px-6 py-4 font-mono text-xs font-bold whitespace-nowrap" style="color: #0F4229;">
                            {{ $periodo->nombre }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $periodo->label }}</td>
                        <td class="px-6 py-4 text-gray-600 text-xs">{{ $periodo->fecha_inicio->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-gray-600 text-xs">{{ $periodo->fecha_fin->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold"
                                  style="background-color: #f0f9f4; color: #0F4229;">
                                {{ $periodo->grupos_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($periodo->estado === 'activo')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                      style="background-color: #0F4229;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                    Activo
                                </span>
                            @elseif($periodo->estado === 'proximo')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                      style="background-color: #EFAD5A;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                    Próximo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                    Cerrado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Editar --}}
                                <button onclick="abrirEditar({{ $periodo->id }}, '{{ $periodo->nombre }}', '{{ addslashes($periodo->label) }}', '{{ $periodo->fecha_inicio->format('Y-m-d') }}', '{{ $periodo->fecha_fin->format('Y-m-d') }}')"
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

                                {{-- Activar --}}
                                @if($periodo->estado !== 'activo')
                                <form method="POST" action="{{ route('admin.periodos.activar', $periodo->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                            style="background-color: #EFAD5A;"
                                            onmouseover="this.style.backgroundColor='#e09a3a'"
                                            onmouseout="this.style.backgroundColor='#EFAD5A'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Activar
                                    </button>
                                </form>
                                @endif

                                {{-- Cerrar --}}
                                @if($periodo->estado === 'activo')
                                <form method="POST" action="{{ route('admin.periodos.cerrar', $periodo->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-gray-400 hover:bg-gray-500 transition-colors duration-150">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        Cerrar
                                    </button>
                                </form>
                                @endif

                                {{-- Eliminar --}}
                                @if($periodo->grupos_count === 0)
                                <form method="POST" action="{{ route('admin.periodos.destroy', $periodo->id) }}"
                                      onsubmit="return confirm('¿Eliminar el periodo \"{{ addslashes($periodo->label) }}\"? Esta acción no se puede deshacer.')">
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
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                            No hay periodos registrados. Crea el primero.
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
                <input type="text" id="crear-nombre" name="nombre" placeholder="ej: 2026-2"
                       pattern="\d{4}-\d+" title="Formato: año-número (ej: 2026-2)" maxlength="10"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                       required>
                <p class="text-xs text-gray-400 mt-1">Formato requerido: año-número (ej: 2026-2)</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                <input type="text" name="label" placeholder="ej: Segundo Cuatrimestre 2026"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                       required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha_inicio"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha_fin"
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
                       pattern="\d{4}-\d+" title="Formato: año-número (ej: 2026-2)" maxlength="10"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                       required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                <input type="text" id="edit-label" name="label"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                       required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio <span class="text-red-500">*</span></label>
                    <input type="date" id="edit-fecha-inicio" name="fecha_inicio"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin <span class="text-red-500">*</span></label>
                    <input type="date" id="edit-fecha-fin" name="fecha_fin"
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
function abrirEditar(id, nombre, label, fechaInicio, fechaFin) {
    document.getElementById('form-editar').action = '/admin/periodos/' + id;
    document.getElementById('edit-nombre').value = nombre;
    document.getElementById('edit-label').value = label;
    document.getElementById('edit-fecha-inicio').value = fechaInicio;
    document.getElementById('edit-fecha-fin').value = fechaFin;
    document.getElementById('modal-editar').classList.remove('hidden');
}

function formatearClave(input) {
    let val = input.value.replace(/[^\d]/g, '');
    if (val.length > 4) {
        val = val.slice(0, 4) + '-' + val.slice(4);
    }
    input.value = val;
}

document.getElementById('crear-nombre').addEventListener('input', function () {
    formatearClave(this);
});
document.getElementById('edit-nombre').addEventListener('input', function () {
    formatearClave(this);
});

@if($errors->any())
document.getElementById('modal-crear').classList.remove('hidden');
@if(old('nombre'))
document.querySelector('#modal-crear input[name="nombre"]').value = '{{ old('nombre') }}';
@endif
@if(old('label'))
document.querySelector('#modal-crear input[name="label"]').value = '{{ old('label') }}';
@endif
@if(old('fecha_inicio'))
document.querySelector('#modal-crear input[name="fecha_inicio"]').value = '{{ old('fecha_inicio') }}';
@endif
@if(old('fecha_fin'))
document.querySelector('#modal-crear input[name="fecha_fin"]').value = '{{ old('fecha_fin') }}';
@endif
@endif
</script>
@endpush
