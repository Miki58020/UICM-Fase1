@extends('layouts.app')

@section('title', 'Documentos | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{
             filtroEstado: '',
             filtrar() {
                 this.$nextTick(() => {
                     const tarjetas = this.$refs.lista.querySelectorAll('[data-estado]');
                     let visibles = 0;
                     tarjetas.forEach(t => {
                         const mostrar = !this.filtroEstado || t.dataset.estado === this.filtroEstado;
                         t.style.display = mostrar ? '' : 'none';
                         if (mostrar) visibles++;
                     });
                     this.$refs.sinResultados.style.display = visibles === 0 ? '' : 'none';
                 });
             }
         }"
         x-init="filtrar()">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Alumno</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Documentos</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Tarjetas resumen (también filtran la lista) --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <button type="button" @click="filtroEstado = ''; filtrar()"
                    :class="filtroEstado === '' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #6B7280;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Todos</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-600">{{ $items->count() }}</p>
            </button>
            <button type="button" @click="filtroEstado = (filtroEstado === 'vigente' ? '' : 'vigente'); filtrar()"
                    :class="filtroEstado === 'vigente' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Vigentes</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $vigentes }}</p>
            </button>
            <button type="button" @click="filtroEstado = (filtroEstado === 'vencido' ? '' : 'vencido'); filtrar()"
                    :class="filtroEstado === 'vencido' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #dc2626;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Vencidos</p>
                <p class="text-2xl font-extrabold mt-1 text-red-600">{{ $vencidos }}</p>
            </button>
            <button type="button" @click="filtroEstado = (filtroEstado === 'sin_subir' ? '' : 'sin_subir'); filtrar()"
                    :class="filtroEstado === 'sin_subir' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #9ca3af;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Sin subir</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-400">{{ $faltantes }}</p>
            </button>
        </div>

        @php
            $plazoVencido = $plazoMigrado && now()->gt($plazoMigrado);
        @endphp
        @if ($plazoVencido || $vencidos > 0 || $porVencer > 0 || $plazoMigrado)
            @php
                if ($plazoVencido) {
                    [$alertaColor, $alertaBg, $alertaEtiqueta, $alertaMensaje] = ['#dc2626', '#fef2f2', 'Atención',
                        'Tu plazo para completar tus documentos venció el ' . $plazoMigrado->format('d/m/Y') . '. Súbelos lo antes posible.'];
                } elseif ($vencidos > 0) {
                    [$alertaColor, $alertaBg, $alertaEtiqueta, $alertaMensaje] = ['#dc2626', '#fef2f2', 'Atención',
                        $vencidos === 1 ? 'Tienes 1 documento vencido. Actualízalo para mantener tu expediente al día.'
                                        : "Tienes {$vencidos} documentos vencidos. Actualízalos para mantener tu expediente al día."];
                } elseif ($plazoMigrado) {
                    [$alertaColor, $alertaBg, $alertaEtiqueta, $alertaMensaje] = ['#EFAD5A', '#fffaf0', 'Aviso',
                        'Tienes hasta el ' . $plazoMigrado->format('d/m/Y') . ' para completar tus documentos.'];
                } else {
                    [$alertaColor, $alertaBg, $alertaEtiqueta, $alertaMensaje] = ['#EFAD5A', '#fffaf0', 'Aviso',
                        $porVencer === 1 ? '1 documento está próximo a vencer.' : "{$porVencer} documentos están próximos a vencer."];
                }
            @endphp
            <div class="rounded-2xl shadow-md p-4 mb-6 flex flex-wrap items-center gap-3" style="background-color: {{ $alertaBg }};">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold text-white flex-shrink-0"
                      style="background-color: {{ $alertaColor }};">
                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                    {{ $alertaEtiqueta }}
                </span>
                <p class="text-sm" style="color: {{ $alertaColor }};">{{ $alertaMensaje }}</p>
            </div>
        @endif

        <div class="space-y-4" x-ref="lista">

            <div x-ref="sinResultados" style="display: none;" class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                    <p class="text-sm text-gray-500 max-w-xs">Ningún documento coincide con el filtro seleccionado.</p>
                </div>
            </div>

            @foreach ($items as $item)
            @php
                $doc = $item['documento'];
                $estado = !$doc ? 'sin_subir' : ($doc->estaVencido() ? 'vencido' : ($doc->porVencer() ? 'por_vencer' : 'vigente'));
                $badge = [
                    'sin_subir'  => ['#9ca3af', 'Sin subir'],
                    'vigente'    => ['#0F4229', 'Vigente'],
                    'por_vencer' => ['#EFAD5A', 'Próximo a vencer'],
                    'vencido'    => ['#dc2626', 'Vencido'],
                ][$estado];
            @endphp
            <div class="bg-white rounded-2xl shadow-md overflow-hidden" data-estado="{{ $estado }}" x-data="{ subiendo: false }">
                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

                <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm">{{ $item['label'] }}</h3>
                        @if ($doc)
                            <p class="text-xs text-gray-400 mt-0.5">
                                Subido el {{ $doc->fecha_subida->format('d/m/Y') }}
                                @if ($doc->fecha_vigencia)
                                    &nbsp;·&nbsp; Vigente hasta {{ $doc->fecha_vigencia->format('d/m/Y') }}
                                @endif
                            </p>
                        @else
                            <p class="text-xs text-gray-400 mt-0.5">Aún no has subido este documento.</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                              style="background-color: {{ $badge[0] }};">
                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                            {{ $badge[1] }}
                        </span>
                        <button type="button" @click="subiendo = !subiendo"
                                class="text-xs font-bold px-4 py-2 rounded-lg"
                                style="background-color: #0F4229; color: white;">
                            Seleccionar archivo
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50" x-show="subiendo" x-cloak>
                    <form method="POST" action="{{ route('alumno.documentos.subir', $item['tipo']) }}"
                          enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                        @csrf
                        <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required
                               class="flex-1 text-sm text-gray-600 border-2 border-dashed border-gray-200 rounded-xl px-4 py-2.5 outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700">
                        <button type="submit"
                                class="px-4 py-2 rounded-lg text-xs font-bold text-white"
                                style="background-color: #D4AF37;">
                            Subir
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 mt-2">Formatos permitidos: PDF, JPG, PNG. Máximo 5 MB.</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>
@endsection
