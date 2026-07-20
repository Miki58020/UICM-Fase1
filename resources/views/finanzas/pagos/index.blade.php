@extends('layouts.app')

@section('title', 'Pagos en Revisión | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado de módulo --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Finanzas
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Pagos en revisión</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Contadores --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">

            @php $baseParams = request()->except(['estado', 'page']); @endphp
            <a href="{{ route('finanzas.pagos.index', $baseParams) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full block transition-shadow duration-150"
               style="border-color: #6B7280; {{ !request('estado') ? 'box-shadow: 0 0 0 2px #6B7280;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Todos</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">
                    {{ $conteo['total'] }}
                </p>
            </a>

            <a href="{{ route('finanzas.pagos.index', $baseParams + ['estado' => 'pendiente']) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full block transition-shadow duration-150"
               style="border-color: #EFAD5A; {{ request('estado') === 'pendiente' ? 'box-shadow: 0 0 0 2px #EFAD5A;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">En revisión</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">
                    {{ $conteo['pendiente'] }}
                </p>
            </a>

            <a href="{{ route('finanzas.pagos.index', $baseParams + ['estado' => 'aprobado']) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full block transition-shadow duration-150"
               style="border-color: #0F4229; {{ request('estado') === 'aprobado' ? 'box-shadow: 0 0 0 2px #0F4229;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Validados</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">
                    {{ $conteo['aprobado'] }}
                </p>
            </a>

            <a href="{{ route('finanzas.pagos.index', $baseParams + ['estado' => 'rechazado']) }}"
               class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full block transition-shadow duration-150"
               style="border-color: #9ca3af; {{ request('estado') === 'rechazado' ? 'box-shadow: 0 0 0 2px #9ca3af;' : '' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Rechazados</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">
                    {{ $conteo['rechazado'] }}
                </p>
            </a>

        </div>

        {{-- Buscador y filtros --}}
        <form method="GET" class="bg-white rounded-2xl shadow-md px-6 py-5 mb-6 grid grid-cols-1 sm:grid-cols-5 gap-4">
            @if(request('estado'))
                <input type="hidden" name="estado" value="{{ request('estado') }}">
            @endif
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Buscar</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Folio, matrícula o nombre…"
                           class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>
            </div>
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
            <div class="flex items-end gap-2 sm:col-span-5">
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-150"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Filtrar
                </button>
                @if(request('q') || request('concepto') || request('estado') || request('fecha_desde') || request('fecha_hasta'))
                <a href="{{ route('finanzas.pagos.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-100 transition-colors duration-150">
                    Limpiar
                </a>
                @endif
                <a href="{{ route('finanzas.pagos.exportar', request()->query()) }}"
                   class="ml-auto px-4 py-2.5 rounded-xl text-sm font-bold text-white"
                   style="background-color: #D4AF37;">
                    Exportar CSV
                </a>
            </div>
        </form>

        {{-- Card tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col h-[350px]">

            <div class="h-1.5 w-full flex-shrink-0" style="background-color: #0F4229;"></div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <h2 class="text-sm font-semibold text-gray-700">Comprobantes recibidos</h2>
                <span class="text-xs text-gray-400">{{ $pagos->total() }} registros</span>
            </div>

            <div class="overflow-auto flex-1">
                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Referencia</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3">Tipo</th>
                            <th class="px-6 py-3">Monto</th>
                            <th class="px-6 py-3">Fecha</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($pagos as $pago)
                        @php
                            $refTexto     = $pago->aspirante?->folio ?? ($pago->alumno ? $pago->alumno->matricula : '—');
                            $nombreTexto  = $pago->aspirante?->nombre_completo ?? $pago->alumno?->nombre_completo ?? '—';
                            $programaNombre = $pago->aspirante?->programa?->nombre ?? $pago->alumno?->programa?->nombre ?? '—';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-100">

                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-600 whitespace-nowrap">
                                {{ $refTexto }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                                {{ $nombreTexto }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $programaNombre }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgesConcepto = [
                                        'inscripcion'  => 'bg-gray-100 text-gray-600',
                                        'colegiatura'  => 'bg-emerald-100 text-emerald-700',
                                        'cuatrimestre' => 'bg-blue-100 text-blue-700',
                                        'otro'         => 'bg-gray-100 text-gray-600',
                                    ];
                                    $labelsConcepto = [
                                        'inscripcion'  => 'Inscripción',
                                        'colegiatura'  => 'Colegiatura',
                                        'cuatrimestre' => 'Reinscripción',
                                        'otro'         => 'Otro',
                                    ];
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgesConcepto[$pago->concepto] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $labelsConcepto[$pago->concepto] ?? ucfirst($pago->concepto) }}{{ $pago->mes ? ' '.$pago->mes : '' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-bold text-gray-800 whitespace-nowrap">
                                ${{ number_format($pago->monto, 0) }} MXN
                            </td>

                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap text-xs">
                                {{ $pago->fecha_pago?->format('d/m/Y') ?? '—' }}
                            </td>

                            {{-- Badge estado --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($pago->estado === 'pendiente')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #EFAD5A;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        En revisión
                                    </span>
                                @elseif ($pago->estado === 'aprobado')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Validado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #dc2626;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                        Rechazado
                                    </span>
                                @endif
                            </td>

                            {{-- Acción --}}
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('finanzas.pagos.show', $pago->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white
                                          transition-colors duration-150 whitespace-nowrap"
                                   style="background-color: #D4AF37;"
                                   onmouseover="this.style.backgroundColor='#b8962e'"
                                   onmouseout="this.style.backgroundColor='#D4AF37'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                                 -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Revisar
                                </a>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                        @if (request()->anyFilled(['q', 'concepto', 'estado', 'fecha_desde', 'fecha_hasta']))
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                        </svg>
                                        @else
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        @endif
                                    </div>
                                    <h3 class="text-base font-extrabold text-gray-900 mb-1">
                                        {{ request()->anyFilled(['q', 'concepto', 'estado', 'fecha_desde', 'fecha_hasta']) ? 'Sin resultados' : 'Sin comprobantes' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 max-w-xs">
                                        {{ request()->anyFilled(['q', 'concepto', 'estado', 'fecha_desde', 'fecha_hasta']) ? 'No se encontraron pagos con ese criterio.' : 'No hay comprobantes registrados.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if($pagos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex-shrink-0">
                {{ $pagos->links() }}
            </div>
            @endif

        </div>{{-- /card --}}


    </div>
</section>

@endsection
