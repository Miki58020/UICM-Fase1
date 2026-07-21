@extends('layouts.app')

@section('title', 'Finanzas | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{
             busqueda: '',
             filtroEstado: '',
             filtrar() {
                 this.$nextTick(() => {
                     if (!this.$refs.historialLista) return;
                     const q = this.busqueda.toLowerCase();
                     const filas = this.$refs.historialLista.querySelectorAll('[data-concepto]');
                     let visibles = 0;
                     filas.forEach(f => {
                         const pasaBusq = !q || f.dataset.concepto.includes(q) || f.dataset.periodo.includes(q);
                         const pasaEstado = !this.filtroEstado || f.dataset.estado === this.filtroEstado;
                         const mostrar = pasaBusq && pasaEstado;
                         f.style.display = mostrar ? '' : 'none';
                         if (mostrar) visibles++;
                     });
                     this.$refs.historialSinResultados.style.display = visibles === 0 ? '' : 'none';
                 });
             }
         }"
         x-init="filtrar()">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        @php
            $labelsConcepto = [
                'inscripcion'  => 'Inscripción',
                'colegiatura'  => 'Colegiatura',
                'cuatrimestre' => 'Reinscripción',
                'otro'         => 'Otro',
            ];
        @endphp

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Alumno</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Finanzas</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Tarjetas resumen --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: #0F4229;"></div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 mb-4" style="background-color: #f0f9f4;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-extrabold leading-none mb-1" style="color: #0F4229;">${{ number_format($totalPagado, 0) }}</p>
                    <p class="text-xs font-medium text-gray-500">Total pagado MXN</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: #EFAD5A;"></div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 mb-4" style="background-color: #fef4e8;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #EFAD5A;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-extrabold leading-none mb-1" style="color: #EFAD5A;">${{ number_format($montoPendiente, 0) }}</p>
                    <p class="text-xs font-medium text-gray-500">
                        {{ $pendientes->count() }} {{ $pendientes->count() === 1 ? 'pago pendiente' : 'pagos pendientes' }}
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: #D4AF37;"></div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 mb-4" style="background-color: #fdf8ec;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #D4AF37;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-extrabold leading-none mb-1" style="color: #D4AF37;">
                        {{ $proximoVencimiento?->format('d/m/Y') ?? '—' }}
                    </p>
                    <p class="text-xs font-medium text-gray-500">Próximo vencimiento</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: {{ $alCorriente ? '#0F4229' : '#dc2626' }};"></div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 mb-4"
                         style="background-color: {{ $alCorriente ? '#f0f9f4' : '#fef2f2' }};">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: {{ $alCorriente ? '#0F4229' : '#dc2626' }};">
                            @if ($alCorriente)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0 3.75h.007M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @endif
                        </svg>
                    </div>
                    <p class="text-2xl font-extrabold leading-none mb-1" style="color: {{ $alCorriente ? '#0F4229' : '#dc2626' }};">
                        {{ $alCorriente ? 'Al corriente' : 'Atrasado' }}
                    </p>
                    <p class="text-xs font-medium text-gray-500">Estado de cuenta</p>
                </div>
            </div>
        </div>

        {{-- Estado general --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: {{ $alCorriente ? '#0F4229' : '#dc2626' }};"></div>
            <div class="px-6 py-5 flex items-center gap-3">
                @if ($alCorriente)
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white" style="background-color: #0F4229;">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Al corriente
                    </span>
                    <p class="text-sm text-gray-500">No tienes pagos atrasados.</p>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white bg-red-600">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Atrasado
                    </span>
                    <p class="text-sm text-gray-500">
                        Tienes {{ $atrasados->count() }} pago(s) vencido(s). Tu acceso al portal no se ve afectado, pero te recomendamos ponerte al corriente.
                    </p>
                @endif
            </div>
        </div>

        {{-- Recordatorios --}}
        @if ($porVencerPronto->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            @foreach ($porVencerPronto as $pago)
            <div class="bg-white rounded-2xl shadow-sm border border-amber-200 px-5 py-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: #fef4e8;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #D4AF37;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">{{ $labelsConcepto[$pago->concepto] ?? ucfirst($pago->concepto) }}{{ $pago->mes ? ' '.$pago->mes : '' }}</p>
                    <p class="text-sm font-bold text-gray-800">Vence el {{ $pago->fecha_vencimiento->format('d/m/Y') }}</p>
                </div>
                <a href="{{ route('alumno.pagos.pagar', $pago) }}"
                   class="text-xs font-bold px-4 py-2 rounded-lg text-white whitespace-nowrap" style="background-color: #0F4229;">
                    Pagar
                </a>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Pagos pendientes --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #D4AF37;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700">Pagos pendientes</h2>
                <span class="ml-auto text-xs text-gray-400">{{ $pendientes->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                @if ($pendientes->isEmpty())
                    <div class="px-6 py-12 flex flex-col items-center text-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f0f9f4;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin pagos pendientes</h3>
                        <p class="text-sm text-gray-500 max-w-xs">No tienes pagos pendientes por el momento.</p>
                    </div>
                @else
                <div class="min-w-[680px]">
                    <div class="grid grid-cols-[2fr_0.8fr_1.3fr_1fr_1fr_1.3fr] gap-x-4 px-6 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <div>Concepto</div>
                        <div>Periodo</div>
                        <div>Monto</div>
                        <div>Vencimiento</div>
                        <div class="text-center">Estado</div>
                        <div class="text-center">Acción</div>
                    </div>
                    <div class="divide-y divide-gray-100 text-sm">
                        @foreach ($pendientes as $pago)
                        <div class="grid grid-cols-[2fr_0.8fr_1.3fr_1fr_1fr_1.3fr] gap-x-4 px-6 py-4 items-center hover:bg-gray-50">
                            <div class="font-medium text-gray-800 truncate">
                                {{ $labelsConcepto[$pago->concepto] ?? ucfirst($pago->concepto) }}{{ $pago->mes ? ' '.$pago->mes : '' }}
                            </div>
                            <div class="text-gray-500 truncate">{{ $pago->periodo }}</div>
                            <div class="font-bold truncate" style="color: #0F4229;">
                                ${{ number_format($pago->monto, 2) }} MXN
                                @if ($pago->descuento > 0)
                                    <span class="block text-xs font-normal text-gray-400">{{ number_format($pago->descuento, 0) }}% dto. aplicado</span>
                                @endif
                            </div>
                            <div class="text-gray-500 truncate">
                                {{ $pago->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                            </div>
                            <div class="text-center">
                                @if ($pago->mp_payment_id)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #3b82f6;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        En revisión
                                    </span>
                                @elseif ($pago->estaVencido())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #dc2626;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Atrasado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #EFAD5A;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Pendiente
                                    </span>
                                @endif
                            </div>
                            <div class="text-center">
                                @if ($pago->mp_payment_id)
                                    <span class="inline-block text-xs font-semibold px-4 py-2 rounded-lg text-gray-400 bg-gray-100">
                                        Enviado
                                    </span>
                                @else
                                    <a href="{{ route('alumno.pagos.pagar', $pago) }}"
                                       class="inline-block text-xs font-bold px-4 py-2 rounded-lg text-white" style="background-color: #0F4229;">
                                        Pagar
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Buscador y filtro del historial --}}
        @if ($historial->isNotEmpty())
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" x-model="busqueda" @input="filtrar()"
                       placeholder="Buscar por concepto o periodo…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div class="relative sm:w-56">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <select x-model="filtroEstado" @change="filtrar()"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los estados</option>
                    <option value="aprobado">Pagado</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </div>
        </div>
        @endif

        {{-- Historial --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700">Historial de pagos</h2>
                <span class="ml-auto text-xs text-gray-400">{{ $historial->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                @if ($historial->isEmpty())
                    <div class="px-6 py-12 flex flex-col items-center text-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin historial</h3>
                        <p class="text-sm text-gray-500 max-w-xs">Aún no hay pagos en tu historial.</p>
                    </div>
                @else
                <div class="min-w-[680px]" x-ref="historialLista">
                    <div class="grid grid-cols-[2fr_0.8fr_1.3fr_1fr_1fr_1.3fr] gap-x-4 px-6 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <div>Concepto</div>
                        <div>Periodo</div>
                        <div>Monto</div>
                        <div>Fecha de pago</div>
                        <div class="text-center">Estado</div>
                        <div class="text-center">Comprobante</div>
                    </div>
                    <div x-ref="historialSinResultados" style="display: none;" class="px-6 py-12 flex flex-col items-center text-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                        <p class="text-sm text-gray-500 max-w-xs">Ningún pago coincide con la búsqueda o los filtros.</p>
                    </div>
                    <div class="divide-y divide-gray-100 text-sm">
                        @foreach ($historial as $pago)
                        <div class="grid grid-cols-[2fr_0.8fr_1.3fr_1fr_1fr_1.3fr] gap-x-4 px-6 py-4 items-center hover:bg-gray-50"
                             data-concepto="{{ strtolower(($labelsConcepto[$pago->concepto] ?? $pago->concepto).' '.$pago->mes) }}"
                             data-periodo="{{ strtolower($pago->periodo) }}"
                             data-estado="{{ $pago->estado }}">
                            <div class="font-medium text-gray-800 truncate">
                                {{ $labelsConcepto[$pago->concepto] ?? ucfirst($pago->concepto) }}{{ $pago->mes ? ' '.$pago->mes : '' }}
                            </div>
                            <div class="text-gray-500 truncate">{{ $pago->periodo }}</div>
                            <div class="font-bold truncate" style="color: #0F4229;">
                                ${{ number_format($pago->monto, 2) }} MXN
                            </div>
                            <div class="text-gray-500 truncate">
                                {{ $pago->fecha_pago?->format('d/m/Y') ?? '—' }}
                            </div>
                            <div class="text-center">
                                @if ($pago->estado === 'aprobado')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Pagado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white" style="background-color: #dc2626;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Rechazado
                                    </span>
                                @endif
                            </div>
                            <div class="text-center">
                                @if ($pago->estado === 'aprobado')
                                    <a href="{{ route('alumno.comprobante', $pago) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors"
                                       style="background-color: #0F4229;"
                                       onmouseover="this.style.backgroundColor='#0a2e1c'"
                                       onmouseout="this.style.backgroundColor='#0F4229'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                                     a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Comprobante
                                    </a>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection
