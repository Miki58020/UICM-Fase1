@extends('layouts.app')

@section('title', 'Detalle de Pago | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-4xl">

        {{-- Migas de navegación --}}
        <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
            <a href="{{ route('dashboard') }}" class="hover:text-uicm-green transition-colors">Panel</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('finanzas.pagos.index') }}" class="hover:text-uicm-green transition-colors">Pagos en revisión</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-600">{{ $pago->aspirante->folio ?? "Pago #{$pago->id}" }}</span>
        </nav>

        {{-- Flash de éxito --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-green-50" style="border-color: #0F4229;">
                <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Encabezado del expediente --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                        Comprobante de pago
                    </p>
                    <h1 class="text-xl font-extrabold text-gray-900">
                        {{ $pago->aspirante->nombre_completo ?? '—' }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-0.5 font-mono">
                        {{ $pago->aspirante->folio ?? "Pago #{$pago->id}" }}
                    </p>
                </div>

                {{-- Badge estado --}}
                @if ($pago->estado === 'pendiente')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white self-start sm:self-auto"
                          style="background-color: #EFAD5A;">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        En revisión
                    </span>
                @elseif ($pago->estado === 'aprobado')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white self-start sm:self-auto"
                          style="background-color: #0F4229;">
                        <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                        Pago validado
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-gray-600 bg-gray-200 self-start sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                        Rechazado
                    </span>
                @endif
            </div>
        </div>

        {{-- Dos columnas: datos + comprobante --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">

            {{-- Datos del aspirante (3/5) --}}
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Datos del aspirante</h2>
                </div>

                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Nombre completo</dt>
                            <dd class="text-sm font-semibold text-gray-800">
                                {{ $pago->aspirante->nombre_completo ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Folio</dt>
                            <dd class="text-sm font-mono font-bold tracking-widest" style="color: #0F4229;">
                                {{ $pago->aspirante->folio ?? '—' }}
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Programa</dt>
                            <dd class="text-sm font-semibold text-gray-800">
                                {{ $pago->aspirante->programa->nombre ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Monto declarado</dt>
                            <dd class="text-lg font-extrabold" style="color: #0F4229;">
                                ${{ number_format($pago->monto, 0) }} MXN
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Fecha de pago</dt>
                            <dd class="text-sm font-semibold text-gray-800">
                                {{ $pago->fecha_pago->format('d/m/Y') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Concepto</dt>
                            <dd class="text-sm font-semibold text-gray-800 capitalize">{{ $pago->concepto }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Periodo</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $pago->periodo }}</dd>
                        </div>

                    </dl>
                </div>
            </div>

            {{-- Comprobante (2/5) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586
                                 a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6
                                 a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Comprobante</h2>
                </div>

                <div class="px-6 py-5">
                    @if ($pago->comprobante)
                        <div class="relative rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                            <img src="{{ $pago->comprobante }}"
                                 alt="Comprobante de pago"
                                 class="w-full object-contain max-h-64"
                                 onerror="this.style.display='none'; document.getElementById('fallback-{{ $pago->id }}').style.display='flex';">
                            <div id="fallback-{{ $pago->id }}"
                                 class="hidden items-center justify-center flex-col gap-3 py-12 px-4 text-center">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                             a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm text-gray-400 font-medium">Archivo PDF</p>
                                <p class="text-xs text-gray-300">Vista previa no disponible</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ $pago->comprobante }}" target="_blank"
                               class="inline-flex items-center gap-2 text-xs font-semibold transition-colors duration-150"
                               style="color: #D4AF37;"
                               onmouseover="this.style.textDecoration='underline'"
                               onmouseout="this.style.textDecoration='none'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Ver comprobante completo
                            </a>
                        </div>
                    @else
                        <div class="flex items-center justify-center flex-col gap-3 py-12 text-center">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586
                                         a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6
                                         a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-400">Sin comprobante adjunto</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>{{-- /grid --}}

        {{-- Observaciones si está rechazado --}}
        @if ($pago->estado === 'rechazado' && $pago->observaciones)
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-gray-50" style="border-color: #9ca3af;">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Motivo de rechazo</p>
                <p class="text-sm text-gray-700">{{ $pago->observaciones }}</p>
            </div>
        @endif

        {{-- Acciones principales --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Resolución del pago</h2>
            </div>
            <div class="px-6 py-5 flex flex-col gap-4">

                @if ($pago->estado === 'pendiente')

                    <div class="flex flex-col sm:flex-row items-start gap-4">

                        {{-- Aprobar pago --}}
                        <form method="POST" action="{{ route('finanzas.pagos.aprobar', $pago->id) }}"
                              onsubmit="return confirm('¿Confirmas la validación de este pago?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2
                                           px-6 py-3 rounded-xl text-sm font-bold text-white
                                           transition-colors duration-200 shadow-sm"
                                    style="background-color: #0F4229;"
                                    onmouseover="this.style.backgroundColor='#0a2e1c'"
                                    onmouseout="this.style.backgroundColor='#0F4229'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Aprobar pago
                            </button>
                        </form>

                        {{-- Rechazar pago (toggle) --}}
                        <button type="button"
                                onclick="document.getElementById('form-rechazar-pago').classList.toggle('hidden')"
                                class="inline-flex items-center justify-center gap-2
                                       px-6 py-3 rounded-xl text-sm font-bold text-white
                                       bg-gray-400 hover:bg-gray-500 transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Rechazar pago
                        </button>

                    </div>

                    {{-- Formulario de rechazo --}}
                    <div id="form-rechazar-pago" class="hidden">
                        <form method="POST" action="{{ route('finanzas.pagos.rechazar', $pago->id) }}">
                            @csrf
                            @method('PATCH')
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                                Motivo del rechazo <span class="text-red-500">*</span>
                            </label>
                            <textarea name="observaciones" required maxlength="500" rows="3"
                                      placeholder="Describe el motivo por el que se rechaza el comprobante..."
                                      class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 resize-none mb-3"
                                      style="--tw-ring-color: #9ca3af;">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <p class="text-xs text-red-500 mb-2">{{ $message }}</p>
                            @enderror
                            <button type="submit"
                                    class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gray-500 hover:bg-gray-600 transition-colors shadow-sm">
                                Confirmar rechazo
                            </button>
                        </form>
                    </div>

                @else
                    <p class="text-sm text-gray-500 italic">
                        Este pago ya fue procesado ({{ $pago->estado === 'aprobado' ? 'validado' : 'rechazado' }}).
                    </p>
                @endif

                {{-- Volver --}}
                <div>
                    <a href="{{ route('finanzas.pagos.index') }}"
                       class="inline-flex items-center justify-center gap-2
                              px-6 py-3 rounded-xl text-sm font-bold border-2
                              transition-colors duration-200"
                       style="border-color: #0F4229; color: #0F4229;"
                       onmouseover="this.style.backgroundColor='#f0f9f4'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Volver a la lista
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>

@endsection
