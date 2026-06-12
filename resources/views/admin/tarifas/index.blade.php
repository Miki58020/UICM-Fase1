@extends('layouts.app')

@section('title', 'Conceptos de Pago | UICM')

@section('content')

@php
$tabs = [
    'inscripcion'  => [
        'label' => 'Inscripción',
        'desc'  => 'Pago único al registrarse como nuevo estudiante.',
        'color' => '#0F4229',
    ],
    'colegiatura'  => [
        'label' => 'Colegiatura',
        'desc'  => 'Pago mensual durante el ciclo escolar.',
        'color' => '#D4AF37',
    ],
    'cuatrimestre' => [
        'label' => 'Cuatrimestre',
        'desc'  => 'Pago al inicio de cada cuatrimestre.',
        'color' => '#EFAD5A',
    ],
];

$nivelLabel = fn($n) => match($n) {
    'licenciatura' => 'Licenciatura',
    'maestria'     => 'Maestría',
    'doctorado'    => 'Doctorado',
    default        => ucfirst($n),
};

$regInicio = $periodoActivo?->fecha_inicio_registro?->format('Y-m-d');
$regFin    = $periodoActivo?->fecha_fin_registro?->format('Y-m-d');
@endphp

<section class="bg-uicm-gray min-h-screen py-12 px-4">
<div class="container mx-auto px-4 lg:px-12 max-w-3xl">

    {{-- Cabecera --}}
    <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
            Administración
        </p>
        <h1 class="text-2xl font-extrabold text-gray-900">Conceptos de Pago</h1>
        <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'inscripcion' }">

        {{-- Botones de tab --}}
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($tabs as $key => $info)
            <button @click="tab = '{{ $key }}'"
                    :style="tab === '{{ $key }}' ? 'background-color:{{ $info['color'] }};' : ''"
                    :class="tab === '{{ $key }}'
                        ? 'text-white shadow-sm'
                        : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                    class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-150">
                {{ $info['label'] }}
            </button>
            @endforeach
        </div>

        {{-- Paneles --}}
        @foreach($tabs as $key => $info)
        <div x-show="tab === '{{ $key }}'"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: {{ $info['color'] }};"></div>

                <div class="px-6 py-5">
                    <p class="text-sm text-gray-500 mb-5">{{ $info['desc'] }}
                        Los cambios aplican a nuevos pagos inmediatamente.
                    </p>

                    <div class="space-y-3">
                        @forelse($tarifas[$key] ?? [] as $tarifa)
                        <div class="flex items-center gap-3 sm:gap-4 rounded-xl px-3 sm:px-5 py-3 sm:py-4 bg-uicm-gray">

                            {{-- Nivel --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Nivel</p>
                                <p class="font-bold text-gray-900">{{ $nivelLabel($tarifa->nivel) }}</p>
                            </div>

                            {{-- Precios --}}
                            <div class="flex-shrink-0 text-right">
                                @if($tarifa->descuentoVigente())
                                    <div class="flex items-center justify-end gap-2 mb-0.5">
                                        <span class="text-xs text-gray-400 line-through">
                                            ${{ number_format($tarifa->monto, 0) }}
                                        </span>
                                        <span class="text-xs font-bold px-1.5 py-0.5 rounded-lg"
                                              style="background-color:#fef4e8; color:#b45309;">
                                            {{ number_format($tarifa->descuento, 0) }}% dto.
                                        </span>
                                    </div>
                                    <p class="text-base sm:text-xl font-extrabold whitespace-nowrap" style="color:#15803d;">
                                        ${{ number_format($tarifa->precio_final, 0) }} MXN
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Monto</p>
                                    <p class="text-base sm:text-xl font-extrabold whitespace-nowrap" style="color: {{ $info['color'] }};">
                                        ${{ number_format($tarifa->monto, 0) }} MXN
                                    </p>
                                    @if($tarifa->descuento > 0 && $tarifa->descuento_fecha_inicio && $tarifa->descuento_fecha_fin)
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ number_format($tarifa->descuento, 0) }}% dto. programado:
                                            {{ $tarifa->descuento_fecha_inicio->format('d/m/Y') }}
                                            - {{ $tarifa->descuento_fecha_fin->format('d/m/Y') }}
                                        </p>
                                    @endif
                                @endif
                            </div>

                            {{-- Botón editar --}}
                            <button onclick="abrirModal({{ $tarifa->id }}, '{{ $nivelLabel($tarifa->nivel) }}', '{{ $info['label'] }}', '{{ $key }}', {{ $tarifa->monto }}, {{ $tarifa->descuento }}, {{ $tarifa->descuento_fecha_inicio?->format('Y-m-d') ? "'".$tarifa->descuento_fecha_inicio->format('Y-m-d')."'" : 'null' }}, {{ $tarifa->descuento_fecha_fin?->format('Y-m-d') ? "'".$tarifa->descuento_fecha_fin->format('Y-m-d')."'" : 'null' }})"
                                    class="inline-flex items-center gap-2 px-2.5 sm:px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200 flex-shrink-0"
                                    style="background-color: {{ $info['color'] }};"
                                    onmouseover="this.style.opacity='0.85'"
                                    onmouseout="this.style.opacity='1'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span class="hidden sm:inline">Editar</span>
                            </button>
                        </div>
                        @empty
                        <div class="text-center py-8 text-sm text-gray-400">
                            No hay tarifas configuradas para este concepto.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>

</div>
</section>

{{-- Modal de edición --}}
<div id="modal-editar" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

        <div class="px-6 py-5">
            <h2 class="text-base font-extrabold text-gray-900 mb-0.5">Editar tarifa</h2>
            <p id="modal-info" class="text-sm text-gray-500 mb-5"></p>

            <form id="form-editar" method="POST">
                @csrf
                @method('PATCH')

                {{-- Monto --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Monto (MXN)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                        <input type="number" name="monto" id="modal-monto"
                               min="1" max="99999" step="1" placeholder="1500" required
                               oninput="actualizarPreview()"
                               class="w-full pl-7 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:border-transparent"
                               style="--tw-ring-color: #0F4229;">
                    </div>
                </div>

                {{-- Descuento y vigencia --}}
                <div class="mb-4 p-4 rounded-xl bg-uicm-gray space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Descuento (%)
                        </label>
                        <div class="relative">
                            <input type="number" name="descuento" id="modal-descuento"
                                   min="0" max="100" step="0.01" placeholder="0" required
                                   oninput="actualizarPreview()"
                                   class="w-full pl-4 pr-8 py-2.5 border border-gray-200 rounded-xl text-sm font-bold bg-white focus:outline-none focus:ring-2 focus:border-transparent"
                                   style="--tw-ring-color: #0F4229;">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Ingresa 0 si no aplica descuento.</p>
                    </div>

                    {{-- Vigencia del descuento (solo Inscripción) --}}
                    <div id="modal-descuento-fechas" class="hidden">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Vigencia del descuento
                        </label>

                        @if($regInicio && $regFin)
                            <span class="inline-block mb-3 text-xs font-bold px-2 py-1 rounded-lg"
                                  style="background-color:#fef4e8; color:#b45309;">
                                Rango permitido: {{ \Illuminate\Support\Carbon::parse($regInicio)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($regFin)->format('d/m/Y') }}
                            </span>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Desde</label>
                                    <input type="date" name="descuento_fecha_inicio" id="modal-descuento-inicio"
                                           min="{{ $regInicio }}" max="{{ $regFin }}"
                                           class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold bg-white focus:outline-none focus:ring-2 focus:border-transparent"
                                           style="--tw-ring-color: #0F4229;">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">Hasta</label>
                                    <input type="date" name="descuento_fecha_fin" id="modal-descuento-fin"
                                           min="{{ $regInicio }}" max="{{ $regFin }}"
                                           class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold bg-white focus:outline-none focus:ring-2 focus:border-transparent"
                                           style="--tw-ring-color: #0F4229;">
                                </div>
                            </div>
                        @else
                            <span class="inline-block text-xs font-bold px-2 py-1 rounded-lg"
                                  style="background-color:#fef4e8; color:#b45309;">
                                Configura primero el rango de inscripción del cuatrimestre activo para poder programar un descuento.
                            </span>
                            <input type="hidden" name="descuento_fecha_inicio" id="modal-descuento-inicio" value="">
                            <input type="hidden" name="descuento_fecha_fin" id="modal-descuento-fin" value="">
                        @endif
                    </div>
                </div>

                {{-- Preview precio final --}}
                <div class="mb-5 px-4 py-3 rounded-xl flex items-center justify-between" style="background-color:#f0f9f4;">
                    <span class="text-xs font-semibold text-gray-500">Precio final</span>
                    <span id="preview-valor" class="text-base font-extrabold" style="color:#0F4229;">$0 MXN</span>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="cerrarModal()"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors duration-150">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-200"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModal(id, nivel, tipo, tipoKey, monto, descuento, descInicio, descFin) {
    document.getElementById('modal-info').textContent = nivel + ' — ' + tipo;
    document.getElementById('modal-monto').value = monto;
    document.getElementById('modal-descuento').value = descuento;
    document.getElementById('form-editar').action = '/admin/tarifas/' + id;

    const fechasWrap = document.getElementById('modal-descuento-fechas');
    const inputInicio = document.getElementById('modal-descuento-inicio');
    const inputFin    = document.getElementById('modal-descuento-fin');

    if (tipoKey === 'inscripcion') {
        fechasWrap.classList.remove('hidden');
    } else {
        fechasWrap.classList.add('hidden');
    }
    inputInicio.value = descInicio || '';
    inputFin.value    = descFin || '';

    actualizarPreview();
    document.getElementById('modal-editar').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modal-editar').classList.add('hidden');
}

function actualizarPreview() {
    const monto     = parseFloat(document.getElementById('modal-monto').value) || 0;
    const descuento = parseFloat(document.getElementById('modal-descuento').value) || 0;
    const final     = Math.round(monto * (1 - descuento / 100));
    document.getElementById('preview-valor').textContent =
        '$' + final.toLocaleString('es-MX') + ' MXN';
}

document.getElementById('modal-editar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
@endpush

@endsection
