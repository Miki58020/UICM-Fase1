@extends('layouts.app')

@section('title', 'Pagos en Revisión | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado de módulo --}}
        <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                    Finanzas
                </p>
                <h1 class="text-2xl font-extrabold text-gray-900">Pagos en revisión</h1>
                <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('finanzas.estadisticas') }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold border-2 transition-colors"
                   style="border-color: #0F4229; color: #0F4229;">
                    Estadísticas
                </a>
                <a href="{{ route('finanzas.alumnos') }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold border-2 transition-colors"
                   style="border-color: #0F4229; color: #0F4229;">
                    Alumnos al corriente
                </a>
            </div>
        </div>

        {{-- Filtros de servidor --}}
        <form method="GET" class="bg-white rounded-2xl shadow-md px-6 py-5 mb-6 grid grid-cols-1 sm:grid-cols-5 gap-4">
            @if(request('estado'))
                <input type="hidden" name="estado" value="{{ request('estado') }}">
            @endif
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Buscar</label>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Folio, matrícula o nombre…"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Concepto</label>
                <select name="concepto" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white">
                    <option value="">Todos</option>
                    @foreach (['inscripcion' => 'Inscripción', 'reinscripcion' => 'Reinscripción', 'colegiatura' => 'Colegiatura', 'cuatrimestre' => 'Cuatrimestre', 'otro' => 'Otro'] as $val => $label)
                        <option value="{{ $val }}" {{ request('concepto') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white">
            </div>
            <div class="flex items-end gap-2 sm:col-span-5">
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-white"
                        style="background-color: #0F4229;">
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
                            $refTexto     = $pago->aspirante?->folio ?? ($pago->alumno ? 'MAT-'.$pago->alumno->matricula : '—');
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
                                        'inscripcion'   => 'bg-gray-100 text-gray-600',
                                        'reinscripcion' => 'bg-blue-100 text-blue-700',
                                        'colegiatura'   => 'bg-emerald-100 text-emerald-700',
                                        'cuatrimestre'  => 'bg-purple-100 text-purple-700',
                                        'otro'          => 'bg-gray-100 text-gray-600',
                                    ];
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgesConcepto[$pago->concepto] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($pago->concepto) }}{{ $pago->mes ? ' '.$pago->mes : '' }}
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
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-400">
                                {{ request()->anyFilled(['q', 'concepto', 'estado', 'fecha_desde', 'fecha_hasta']) ? 'No se encontraron pagos con ese criterio.' : 'No hay comprobantes registrados.' }}
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
