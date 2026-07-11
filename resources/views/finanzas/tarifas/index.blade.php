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
$finClases = $periodoActivo?->fecha_fin_clases?->format('Y-m-d');
@endphp

<section class="bg-uicm-gray min-h-screen py-12 px-4">
<div class="container mx-auto px-4 lg:px-12 max-w-7xl">

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
                                    @if($key === 'colegiatura')
                                        @if($tarifa->descuento > 0 && $tarifa->dias_descuento_pronto_pago)
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ number_format($tarifa->descuento, 0) }}% dto. si paga del día 1 al {{ $tarifa->dias_descuento_pronto_pago }} de cada mes
                                            </p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            Atraso después del día {{ $tarifa->dia_limite_pago ?? '—' }} de cada mes
                                        </p>
                                    @endif
                                    @if($key === 'cuatrimestre' && $tarifa->dias_anticipacion_cobro && $tarifa->dias_para_pagar)
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            Cobro {{ $tarifa->dias_anticipacion_cobro }} día(s) antes de fin de clases, {{ $tarifa->dias_para_pagar }} día(s) para pagar
                                        </p>
                                    @endif
                                @endif
                            </div>

                            {{-- Botón editar --}}
                            <button onclick="abrirModal({{ $tarifa->id }}, '{{ $nivelLabel($tarifa->nivel) }}', '{{ $info['label'] }}', '{{ $key }}', {{ $tarifa->monto }}, {{ $tarifa->descuento }}, {{ $tarifa->descuento_fecha_inicio?->format('Y-m-d') ? "'".$tarifa->descuento_fecha_inicio->format('Y-m-d')."'" : 'null' }}, {{ $tarifa->descuento_fecha_fin?->format('Y-m-d') ? "'".$tarifa->descuento_fecha_fin->format('Y-m-d')."'" : 'null' }}, {{ $tarifa->dia_limite_pago ?? 'null' }}, {{ $tarifa->dias_descuento_pronto_pago ?? 'null' }}, {{ $tarifa->dias_anticipacion_cobro ?? 'null' }}, {{ $tarifa->dias_para_pagar ?? 'null' }})"
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

                    {{-- Días de cobro (solo Cuatrimestre) --}}
                    <div id="modal-cuatri-dias" class="hidden">
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Días de anticipación a fin de clases</label>
                                <input type="number" name="dias_anticipacion_cobro" id="modal-dias-anticipacion"
                                       min="1" max="90" oninput="actualizarVentanaCuatrimestre()"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold bg-white focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Días para pagar</label>
                                <input type="number" name="dias_para_pagar" id="modal-dias-para-pagar"
                                       min="1" max="60" oninput="actualizarVentanaCuatrimestre()"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold bg-white focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>
                        </div>
                        @if(!$finClases)
                            <p class="text-xs font-bold px-2 py-1 rounded-lg inline-block" style="background-color:#fef4e8; color:#b45309;">
                                Configura primero la fecha de fin de clases del cuatrimestre activo.
                            </p>
                        @endif
                    </div>

                    {{-- Vigencia del descuento (Inscripción / Cuatrimestre) --}}
                    <div id="modal-descuento-fechas" class="hidden">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Vigencia del descuento
                        </label>

                        {{-- Badge estático: inscripción (rango fijo del periodo de registro) --}}
                        <span id="modal-rango-inscripcion" class="hidden inline-block mb-3 text-xs font-bold px-2 py-1 rounded-lg"
                              style="background-color:#fef4e8; color:#b45309;">
                            @if($regInicio && $regFin)
                                Rango permitido: {{ \Illuminate\Support\Carbon::parse($regInicio)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($regFin)->format('d/m/Y') }}
                            @else
                                Configura primero el rango de inscripción del cuatrimestre activo para poder programar un descuento.
                            @endif
                        </span>

                        {{-- Badge dinámico: cuatrimestre (calculado en JS a partir de los días de cobro) --}}
                        <span id="modal-rango-cuatrimestre" class="hidden inline-block mb-3 text-xs font-bold px-2 py-1 rounded-lg"
                              style="background-color:#fef4e8; color:#b45309;">
                            Define los días de anticipación y para pagar para calcular la ventana.
                        </span>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Desde</label>
                                <input type="date" name="descuento_fecha_inicio" id="modal-descuento-inicio"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold bg-white focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Hasta</label>
                                <input type="date" name="descuento_fecha_fin" id="modal-descuento-fin"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold bg-white focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>
                        </div>
                    </div>

                    {{-- Días del mes (solo Colegiatura) --}}
                    <div id="modal-descuento-dias" class="hidden">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Días con descuento (desde el 1°)</label>
                                <input type="number" name="dias_descuento_pronto_pago" id="modal-dias-descuento"
                                       min="1" max="28"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold bg-white focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Día límite de pago</label>
                                <input type="number" name="dia_limite_pago" id="modal-dia-limite"
                                       min="1" max="28" required
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold bg-white focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Después del día límite, el pago se marca como atrasado (sin suspender el acceso).</p>
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
const FIN_CLASES = {{ $finClases ? "'".$finClases."'" : 'null' }};
let modalTipoKey = null;

function abrirModal(id, nivel, tipo, tipoKey, monto, descuento, descInicio, descFin, diaLimite, diasDescuento, diasAnticipacion, diasParaPagar) {
    modalTipoKey = tipoKey;
    document.getElementById('modal-info').textContent = nivel + ' — ' + tipo;
    document.getElementById('modal-monto').value = monto;
    document.getElementById('modal-descuento').value = descuento;
    document.getElementById('form-editar').action = '/finanzas/tarifas/' + id;

    const fechasWrap = document.getElementById('modal-descuento-fechas');
    const inputInicio = document.getElementById('modal-descuento-inicio');
    const inputFin    = document.getElementById('modal-descuento-fin');
    const diasWrap     = document.getElementById('modal-descuento-dias');
    const inputDiaLimite = document.getElementById('modal-dia-limite');
    const inputDiasDescuento = document.getElementById('modal-dias-descuento');
    const cuatriDiasWrap = document.getElementById('modal-cuatri-dias');
    const inputDiasAnticipacion = document.getElementById('modal-dias-anticipacion');
    const inputDiasParaPagar = document.getElementById('modal-dias-para-pagar');
    const badgeInscripcion = document.getElementById('modal-rango-inscripcion');
    const badgeCuatrimestre = document.getElementById('modal-rango-cuatrimestre');

    if (tipoKey === 'inscripcion' || tipoKey === 'cuatrimestre') {
        fechasWrap.classList.remove('hidden');
    } else {
        fechasWrap.classList.add('hidden');
    }
    badgeInscripcion.classList.toggle('hidden', tipoKey !== 'inscripcion');
    badgeCuatrimestre.classList.toggle('hidden', tipoKey !== 'cuatrimestre');

    inputInicio.value = descInicio || '';
    inputFin.value    = descFin || '';

    if (tipoKey === 'inscripcion') {
        inputInicio.min = inputFin.min = '{{ $regInicio }}';
        inputInicio.max = inputFin.max = '{{ $regFin }}';
    }

    if (tipoKey === 'colegiatura') {
        diasWrap.classList.remove('hidden');
    } else {
        diasWrap.classList.add('hidden');
    }
    inputDiaLimite.value = diaLimite || '';
    inputDiasDescuento.value = diasDescuento || '';

    if (tipoKey === 'cuatrimestre') {
        cuatriDiasWrap.classList.remove('hidden');
    } else {
        cuatriDiasWrap.classList.add('hidden');
    }
    inputDiasAnticipacion.value = diasAnticipacion || '';
    inputDiasParaPagar.value = diasParaPagar || '';

    if (tipoKey === 'cuatrimestre') {
        actualizarVentanaCuatrimestre();
    }

    actualizarPreview();
    document.getElementById('modal-editar').classList.remove('hidden');
}

function actualizarVentanaCuatrimestre() {
    if (modalTipoKey !== 'cuatrimestre') return;

    const badge = document.getElementById('modal-rango-cuatrimestre');
    const inputInicio = document.getElementById('modal-descuento-inicio');
    const inputFin    = document.getElementById('modal-descuento-fin');
    const anticipacion = parseInt(document.getElementById('modal-dias-anticipacion').value) || 0;
    const paraPagar     = parseInt(document.getElementById('modal-dias-para-pagar').value) || 0;

    if (!FIN_CLASES || !anticipacion || !paraPagar) {
        badge.textContent = 'Define los días de anticipación y para pagar para calcular la ventana.';
        inputInicio.removeAttribute('min');
        inputInicio.removeAttribute('max');
        inputFin.removeAttribute('min');
        inputFin.removeAttribute('max');
        return;
    }

    const fin = new Date(FIN_CLASES + 'T00:00:00');
    const apertura = new Date(fin);
    apertura.setDate(apertura.getDate() - anticipacion);
    const vencimiento = new Date(apertura);
    vencimiento.setDate(vencimiento.getDate() + paraPagar);

    const toIso = (d) => d.toISOString().slice(0, 10);
    const toEs  = (d) => toIso(d).split('-').reverse().join('/');

    badge.textContent = 'Ventana de pago: ' + toEs(apertura) + ' - ' + toEs(vencimiento);
    inputInicio.min = inputFin.min = toIso(apertura);
    inputInicio.max = inputFin.max = toIso(vencimiento);
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
