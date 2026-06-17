@extends('layouts.app')

@section('title', 'Carreras del periodo | UICM')

@section('content')

@php
$nivelCfg = [
    'licenciatura' => ['label' => 'Licenciaturas', 'badge' => 'Licenciatura', 'color' => '#0F4229'],
    'maestria'     => ['label' => 'Maestrías',     'badge' => 'Maestría',     'color' => '#D4AF37'],
    'doctorado'    => ['label' => 'Doctorado',     'badge' => 'Doctorado',    'color' => '#9ca3af'],
];
$agrupados = $periodo->programas->groupBy('nivel');
@endphp

<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{ tab: 'todas' }">
<div class="container mx-auto px-4 lg:px-12 max-w-7xl">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Coordinación · <a href="{{ route('admin.periodos.index') }}" class="hover:underline">Cuatrimestres</a>
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Carreras del periodo</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $periodo->label }} <span class="font-mono text-xs">({{ $periodo->nombre }})</span></p>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>
        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a href="{{ route('admin.periodos.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Periodos
            </a>
            @if($programasDisponibles->isNotEmpty())
            <button onclick="document.getElementById('modal-agregar').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200"
                    style="background-color: #0F4229;"
                    onmouseover="this.style.backgroundColor='#0a2e1c'"
                    onmouseout="this.style.backgroundColor='#0F4229'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar carrera
            </button>
            @endif
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white"
         style="background-color: #0F4229;">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white bg-red-500">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Cards resumen --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @php
            $totalProgramas = $periodo->programas->count();
            $activos        = $periodo->programas->where('pivot.activo', true)->count();
            $totalGrupos    = $gruposPorPrograma->flatten()->count();
            $totalAlumnos   = $gruposPorPrograma->flatten()->sum('alumnos_count');
        @endphp
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Carreras</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $totalProgramas }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Activas</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">{{ $activos }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Grupos</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $totalGrupos }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #9ca3af;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Alumnos</p>
            <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $totalAlumnos }}</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-white rounded-2xl shadow-sm p-1 mb-6 w-full sm:w-fit">
        <button @click="tab = 'todas'"
                :class="tab === 'todas' ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                :style="tab === 'todas' ? 'background-color:#0F4229' : ''"
                class="flex-1 sm:flex-none px-3 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150">
            Todas ({{ $totalProgramas }})
        </button>
        @foreach($nivelCfg as $nivel => $cfg)
        @if($agrupados->has($nivel))
        <button @click="tab = '{{ $nivel }}'"
                :class="tab === '{{ $nivel }}' ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                :style="tab === '{{ $nivel }}' ? 'background-color:{{ $cfg['color'] }}' : ''"
                class="flex-1 sm:flex-none px-3 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150">
            {{ $cfg['label'] }} ({{ $agrupados[$nivel]->count() }})
        </button>
        @endif
        @endforeach
    </div>

    {{-- Contenido tabs --}}
    @php
        $nivelesAMostrar = ['todas' => $periodo->programas] + $agrupados->toArray();
    @endphp

    {{-- Tab: Todas --}}
    <div x-show="tab === 'todas'" x-cloak>
        @include('admin.periodos._tabla-carreras', ['programas' => $periodo->programas, 'titulo' => 'Todas las carreras'])
    </div>

    {{-- Tabs por nivel --}}
    @foreach($nivelCfg as $nivel => $cfg)
    @if($agrupados->has($nivel))
    <div x-show="tab === '{{ $nivel }}'" x-cloak>
        @include('admin.periodos._tabla-carreras', ['programas' => $agrupados[$nivel], 'titulo' => $cfg['label']])
    </div>
    @endif
    @endforeach

</div>
</section>

{{-- Modal agregar carrera --}}
<div id="modal-agregar" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Agregar carrera al periodo</h3>
            <button onclick="document.getElementById('modal-agregar').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.periodos.programas.store', $periodo->id) }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Carrera <span class="text-red-500">*</span></label>
                <select name="programa_id"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                        required>
                    <option value="">Seleccionar</option>
                    @php $nivelActual = null; @endphp
                    @foreach($programasDisponibles as $prog)
                        @if($prog->nivel !== $nivelActual)
                            @if($nivelActual !== null)</optgroup>@endif
                            @php $nivelActual = $prog->nivel; @endphp
                            <optgroup label="{{ $nivelCfg[$prog->nivel]['label'] ?? ucfirst($prog->nivel) }}">
                        @endif
                        <option value="{{ $prog->id }}" {{ old('programa_id') == $prog->id ? 'selected' : '' }}>
                            {{ $prog->nombre }}
                        </option>
                    @endforeach
                    @if($nivelActual !== null)</optgroup>@endif
                </select>
                @error('programa_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Grupos a crear <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="num_grupos" value="{{ old('num_grupos') }}" placeholder="Ej. 2" min="1" max="10"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                           required>
                    <p class="text-xs text-gray-400 mt-1">Clave: XXX-{{ $periodo->nombre }}-A, B, C...</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Capacidad por grupo <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="capacidad" value="{{ old('capacidad') }}" placeholder="Ej. 20" min="1" max="500"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                           required>
                    <p class="text-xs text-gray-400 mt-1">Máximo de alumnos por grupo.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-agregar').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Agregar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
@if($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('modal-agregar').classList.remove('hidden');
});
@endif
</script>
@endpush
