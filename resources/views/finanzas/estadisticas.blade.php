@extends('layouts.app')

@section('title', 'Estadísticas | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Finanzas</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Estadísticas de pagos</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Totales --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total recaudado</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">${{ number_format($totalRecaudado, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pendiente por cobrar</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">${{ number_format($montoPendiente, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 border-red-500">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Atrasado</p>
                <p class="text-2xl font-extrabold mt-1 text-red-600">${{ number_format($montoAtrasado, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #9ca3af;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Rechazado</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">${{ number_format($montoRechazado, 2) }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="bg-white rounded-2xl shadow-md px-6 py-5 mb-6 grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Concepto</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    <select name="concepto"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">Todos</option>
                        @foreach (['inscripcion' => 'Inscripción', 'colegiatura' => 'Colegiatura', 'cuatrimestre' => 'Reinscripción', 'otro' => 'Otro'] as $val => $label)
                            <option value="{{ $val }}" {{ request('concepto') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Programa</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <select name="programa"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">Todos</option>
                        @foreach ($programas as $programa)
                            <option value="{{ $programa->id }}" {{ (string) request('programa') === (string) $programa->id ? 'selected' : '' }}>{{ $programa->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div class="flex items-end gap-2 sm:col-span-4">
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-150"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Filtrar
                </button>
                @if(request('concepto') || request('programa') || request('fecha_desde') || request('fecha_hasta'))
                <a href="{{ route('finanzas.estadisticas') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-100 transition-colors duration-150">
                    Limpiar
                </a>
                @endif
            </div>
        </form>

        {{-- Recaudado por mes (gráfica de barras) --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Recaudado por mes</h2>
            </div>
            <div class="px-6 py-6">
                @if ($porMes->isEmpty())
                    <div class="flex flex-col items-center text-center py-6">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm6 0V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2zm6 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin datos</h3>
                        <p class="text-sm text-gray-500 max-w-xs">Sin pagos validados todavía.</p>
                    </div>
                @else
                    @php $maxMes = $porMes->max() ?: 1; @endphp
                    <div class="flex items-end gap-3 sm:gap-5" style="height: 180px;">
                        @foreach ($porMes as $mes => $monto)
                            @php $alturaPct = max(4, round(($monto / $maxMes) * 100)); @endphp
                            <div class="flex-1 flex flex-col items-center justify-end h-full group">
                                <span class="text-xs font-bold text-gray-700 mb-1.5 whitespace-nowrap">
                                    ${{ number_format($monto, 0) }}
                                </span>
                                <div class="w-full max-w-[52px] rounded-t-md transition-opacity duration-150 group-hover:opacity-80"
                                     style="height: {{ $alturaPct }}%; background-color: #0F4229;"
                                     title="{{ $mes }}: ${{ number_format($monto, 2) }} MXN">
                                </div>
                                <span class="text-xs text-gray-400 mt-2 whitespace-nowrap">{{ $mes }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Por concepto (barras horizontales) --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Recaudado por concepto</h2>
                </div>
                <div class="px-6 py-5">
                    @if ($porConcepto->isEmpty())
                        <div class="flex flex-col items-center text-center py-6">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin datos</h3>
                            <p class="text-sm text-gray-500 max-w-xs">Sin pagos validados todavía.</p>
                        </div>
                    @else
                        @php
                            $coloresConcepto = [
                                'inscripcion'  => '#9CA3AF',
                                'colegiatura'  => '#10B981',
                                'cuatrimestre' => '#3B82F6',
                                'otro'         => '#9CA3AF',
                            ];
                            $labelsConcepto = [
                                'inscripcion'  => 'Inscripción',
                                'colegiatura'  => 'Colegiatura',
                                'cuatrimestre' => 'Reinscripción',
                                'otro'         => 'Otro',
                            ];
                            $maxConcepto = $porConcepto->max('monto') ?: 1;
                        @endphp
                        <div class="space-y-4">
                            @foreach ($porConcepto as $concepto => $datos)
                                @php
                                    $color = $coloresConcepto[$concepto] ?? '#9CA3AF';
                                    $label = $labelsConcepto[$concepto] ?? ucfirst($concepto);
                                    $anchoPct = max(4, round(($datos['monto'] / $maxConcepto) * 100));
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-700">
                                            <span class="w-2 h-2 rounded-full inline-block" style="background-color: {{ $color }};"></span>
                                            {{ $label }}
                                            <span class="text-xs text-gray-400 font-normal">({{ $datos['cantidad'] }})</span>
                                        </span>
                                        <span class="text-sm font-bold" style="color: #0F4229;">${{ number_format($datos['monto'], 2) }}</span>
                                    </div>
                                    <div class="w-full h-2.5 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full rounded-full" style="width: {{ $anchoPct }}%; background-color: {{ $color }};"
                                             title="{{ $label }}: ${{ number_format($datos['monto'], 2) }} MXN"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Distribución de estados (dona) --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Distribución de estados</h2>
                </div>
                <div class="px-6 py-5">
                    @php
                        $totalEstados = array_sum($distribucionEstados);
                        $coloresEstado = ['aprobado' => '#0F4229', 'pendiente' => '#EFAD5A', 'rechazado' => '#dc2626'];
                        $etiquetasEstado = ['aprobado' => 'Validado', 'pendiente' => 'En revisión', 'rechazado' => 'Rechazado'];
                        $circunferencia = 2 * M_PI * 60;
                        $offsetAcumulado = 0;
                    @endphp
                    @if ($totalEstados === 0)
                        <div class="flex flex-col items-center text-center py-6">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm6 0V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2zm6 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin datos</h3>
                            <p class="text-sm text-gray-500 max-w-xs">Aún no hay pagos registrados.</p>
                        </div>
                    @else
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <svg width="150" height="150" viewBox="0 0 150 150" class="flex-shrink-0 -rotate-90">
                                <circle cx="75" cy="75" r="60" fill="none" stroke="#f3f4f6" stroke-width="18"/>
                                @foreach ($distribucionEstados as $estado => $cantidad)
                                    @continue($cantidad === 0)
                                    @php
                                        $porcion = $cantidad / $totalEstados;
                                        $largo = $porcion * $circunferencia;
                                        $offset = $offsetAcumulado;
                                        $offsetAcumulado += $largo;
                                    @endphp
                                    <circle cx="75" cy="75" r="60" fill="none" stroke="{{ $coloresEstado[$estado] }}"
                                            stroke-width="18"
                                            stroke-dasharray="{{ $largo }} {{ $circunferencia - $largo }}"
                                            stroke-dashoffset="{{ -$offset }}">
                                        <title>{{ $etiquetasEstado[$estado] }}: {{ $cantidad }} pago(s)</title>
                                    </circle>
                                @endforeach
                                <text x="75" y="75" transform="rotate(90 75 75)" text-anchor="middle" dominant-baseline="middle"
                                      class="font-extrabold" style="font-size: 22px; fill: #0F4229;">{{ $totalEstados }}</text>
                            </svg>
                            <div class="flex-1 w-full space-y-2.5">
                                @foreach ($distribucionEstados as $estado => $cantidad)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="inline-flex items-center gap-2 text-gray-700">
                                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: {{ $coloresEstado[$estado] }};"></span>
                                            {{ $etiquetasEstado[$estado] }}
                                        </span>
                                        <span class="font-semibold text-gray-600">
                                            {{ $cantidad }} <span class="text-gray-400 font-normal">({{ $totalEstados ? round($cantidad / $totalEstados * 100) : 0 }}%)</span>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</section>
@endsection
