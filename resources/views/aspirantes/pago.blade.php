@extends('layouts.app')

@section('title', 'Pago de Inscripción | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 max-w-2xl">

        {{-- Encabezado --}}
        <div class="text-center mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: #D4AF37;">
                Admisiones
            </p>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Pago de Inscripción</h1>
            <div class="mx-auto w-16 h-1 rounded-full" style="background-color: #D4AF37;"></div>
        </div>

        <div class="space-y-6">

            {{-- ══════════════════════════════════════
                 CARD: Datos del aspirante
            ══════════════════════════════════════ --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                <div class="px-6 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Aspirante</p>
                            <h2 class="text-lg font-extrabold text-gray-900">{{ $aspirante->nombre_completo }}</h2>
                        </div>
                        {{-- Badge estado --}}
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white self-start sm:self-auto"
                              style="background-color: #EFAD5A;">
                            <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                            Pago pendiente
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5">
                        <div class="bg-uicm-gray rounded-xl p-4">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Folio</p>
                            <p class="font-bold font-mono tracking-widest text-sm" style="color: #0F4229;">
                                {{ $aspirante->folio }}
                            </p>
                        </div>
                        <div class="bg-uicm-gray rounded-xl p-4">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Programa</p>
                            <p class="font-semibold text-sm text-gray-800">{{ $aspirante->programa->nombre ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════
                 CARD: Instrucciones de pago
            ══════════════════════════════════════ --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #D4AF37;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10
                                 a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700">Instrucciones de pago</h3>
                </div>

                <div class="px-6 py-5">

                    {{-- Monto destacado --}}
                    <div class="text-center mb-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Monto a pagar</p>
                        <p class="text-4xl font-extrabold" style="color: #0F4229;">$3,500 MXN</p>
                    </div>

                    {{-- Caja de datos bancarios — borde izquierdo dorado --}}
                    <div class="rounded-xl p-5 border-l-4 bg-yellow-50"
                         style="border-color: #D4AF37;">

                        <p class="text-xs font-bold uppercase tracking-widest mb-4" style="color: #D4AF37;">
                            Datos bancarios
                        </p>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-sm">
                            <div>
                                <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-0.5">Banco</dt>
                                <dd class="font-bold text-gray-800">BBVA</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-0.5">Número de cuenta</dt>
                                <dd class="font-bold text-gray-800 font-mono">0123456789</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-0.5">CLABE interbancaria</dt>
                                <dd class="font-bold text-gray-800 font-mono tracking-widest">012345678901234567</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-0.5">Referencia</dt>
                                <dd class="font-bold font-mono tracking-widest" style="color: #0F4229;">
                                    {{ $aspirante->folio }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <p class="text-sm text-gray-500 mt-4 leading-relaxed">
                        Realiza la transferencia y sube tu comprobante. La referencia debe coincidir
                        exactamente con tu número de folio.
                    </p>

                </div>
            </div>

            {{-- ══════════════════════════════════════
                 CARD: Subir comprobante
            ══════════════════════════════════════ --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700">Subir comprobante de pago</h3>
                </div>

                <div class="px-6 py-5">
                    <form method="POST"
                          action="{{ route('aspirantes.pago.enviar') }}"
                          enctype="multipart/form-data"
                          x-data="{ archivo: null, subiendo: false }"
                          @submit="subiendo = true">
                        @csrf
                        <input type="hidden" name="folio" value="{{ $aspirante->folio }}">

                        @error('comprobante')
                        <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-4 text-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </div>
                        @enderror

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Archivo del comprobante <span class="text-red-500">*</span>
                            </label>

                            {{-- Zona de carga — cambia visualmente al seleccionar --}}
                            <label for="comprobante"
                                   class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-xl cursor-pointer transition-colors duration-150"
                                   :class="archivo ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'">

                                <template x-if="!archivo">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-7 h-7 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="text-xs text-gray-500">
                                            <span class="font-semibold" style="color: #0F4229;">Haz clic para seleccionar</span>
                                            &nbsp;o arrastra aquí
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">PDF, JPG o PNG — máx. 5 MB</p>
                                    </div>
                                </template>

                                <template x-if="archivo">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-7 h-7 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-xs font-semibold text-green-700" x-text="archivo"></p>
                                        <p class="text-xs text-gray-400 mt-0.5">Haz clic para cambiar</p>
                                    </div>
                                </template>
                            </label>

                            <input type="file"
                                   id="comprobante"
                                   name="comprobante"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="sr-only"
                                   @change="archivo = $event.target.files[0]?.name ?? null">
                        </div>

                        <button type="submit"
                                :disabled="!archivo || subiendo"
                                class="w-full py-3 rounded-xl text-white text-sm font-bold transition-colors duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                style="background-color: #0F4229;"
                                onmouseover="if(!this.disabled) this.style.backgroundColor='#0a2e1c'"
                                onmouseout="this.style.backgroundColor='#0F4229'">
                            <span x-show="!subiendo">Enviar comprobante</span>
                            <span x-show="subiendo" class="inline-flex items-center gap-2 justify-center">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                Subiendo...
                            </span>
                        </button>

                    </form>
                </div>
            </div>

            {{-- Volver --}}
            <div class="text-center">
                <a href="{{ route('aspirantes.resultado', ['folio' => $aspirante->folio]) }}"
                   class="text-sm text-gray-400 hover:text-uicm-green transition-colors duration-150
                          inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Regresar a mi estado de inscripción
                </a>
            </div>

        </div>
    </div>
</section>

@endsection