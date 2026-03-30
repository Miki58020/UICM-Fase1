@extends('layouts.app')

@section('title', 'Pagos en Revisión | UICM')

@section('content')

<section
    x-data="{
        busqueda: '',
        filtroEstado: '',
        filtrar() {
            this.$nextTick(() => {
                const filas = this.$refs.tbody.querySelectorAll('tr[data-nombre]');
                let visibles = 0;
                filas.forEach(f => {
                    const texto = this.busqueda.toLowerCase();
                    const pasaBusqueda = !texto || f.dataset.nombre.toLowerCase().includes(texto) || f.dataset.folio.toLowerCase().includes(texto);
                    const pasaEstado  = !this.filtroEstado || f.dataset.estado === this.filtroEstado;
                    const mostrar = pasaBusqueda && pasaEstado;
                    f.style.display = mostrar ? '' : 'none';
                    if (mostrar) visibles++;
                });
                this.$refs.sinResultados.style.display = visibles === 0 ? '' : 'none';
                this.$refs.contadorVisible.textContent = visibles + ' registro' + (visibles !== 1 ? 's' : '');
            });
        }
    }"
    class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado de módulo --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Finanzas
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Pagos en revisión</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Flash de éxito --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-green-50" style="border-color: #0F4229;">
                <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Contadores --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">

            <button type="button"
                    @click="filtroEstado = ''; filtrar()"
                    :class="filtroEstado === '' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #6B7280;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Todos</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">
                    {{ $pagos->count() }}
                </p>
            </button>

            <button type="button"
                    @click="filtroEstado = 'pendiente'; filtrar()"
                    :class="filtroEstado === 'pendiente' ? 'ring-2' : 'hover:shadow-md'"
                    :style="filtroEstado === 'pendiente' ? 'ring-color: #EFAD5A;' : ''"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">En revisión</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">
                    {{ $pagos->where('estado', 'pendiente')->count() }}
                </p>
            </button>

            <button type="button"
                    @click="filtroEstado = 'aprobado'; filtrar()"
                    :class="filtroEstado === 'aprobado' ? 'ring-2 ring-green-700' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Validados</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">
                    {{ $pagos->where('estado', 'aprobado')->count() }}
                </p>
            </button>

            <button type="button"
                    @click="filtroEstado = 'rechazado'; filtrar()"
                    :class="filtroEstado === 'rechazado' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #9ca3af;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Rechazados</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">
                    {{ $pagos->where('estado', 'rechazado')->count() }}
                </p>
            </button>

        </div>

        {{-- Búsqueda y filtro --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" x-model="busqueda" @input="filtrar()"
                       placeholder="Buscar por folio o nombre…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div class="relative sm:w-52">
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
                    <option value="pendiente">En revisión</option>
                    <option value="aprobado">Validado</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </div>
        </div>

        {{-- Card tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Comprobantes recibidos</h2>
                <span class="text-xs text-gray-400" x-ref="contadorVisible">{{ $pagos->count() }} registros</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Folio</th>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3">Monto</th>
                            <th class="px-6 py-3">Fecha</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100" x-ref="tbody">
                        @forelse ($pagos as $pago)
                        <tr class="hover:bg-gray-50 transition-colors duration-100"
                            data-folio="{{ strtolower($pago->aspirante->folio ?? '') }}"
                            data-nombre="{{ strtolower($pago->aspirante->nombre_completo ?? '') }}"
                            data-estado="{{ $pago->estado }}">

                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-600 whitespace-nowrap">
                                {{ $pago->aspirante->folio ?? '—' }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                                {{ $pago->aspirante->nombre_completo ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $pago->aspirante->programa->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4 font-bold text-gray-800 whitespace-nowrap">
                                ${{ number_format($pago->monto, 0) }} MXN
                            </td>

                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap text-xs">
                                {{ $pago->fecha_pago->format('d/m/Y') }}
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
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
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
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                                No hay comprobantes registrados.
                            </td>
                        </tr>
                        @endforelse
                        <tr x-ref="sinResultados" style="display:none;">
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                                No se encontraron pagos con ese criterio.
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

        </div>{{-- /card --}}

        {{-- Volver --}}
        <div class="mt-6">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 text-sm font-medium transition-colors duration-150"
               style="color: #0F4229;"
               onmouseover="this.style.textDecoration='underline'"
               onmouseout="this.style.textDecoration='none'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al panel
            </a>
        </div>

    </div>
</section>

@endsection
