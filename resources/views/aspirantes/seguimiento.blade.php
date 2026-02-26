@extends('layouts.app')

@section('title', 'Consultar Estado de Inscripción | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen flex items-center py-16">
    <div class="container mx-auto px-8 lg:px-24">

        <div class="max-w-lg mx-auto">

            {{-- Encabezado --}}
            <div class="text-center mb-8">
                <p class="text-uicm-gold text-xs font-bold uppercase tracking-widest mb-2">Admisiones</p>
                <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Consulta tu estado de inscripción</h1>
                <div class="mx-auto w-16 h-1 rounded-full" style="background-color: #D4AF37;"></div>
            </div>

            {{-- Error flash --}}
            @if ($errors->has('folio'))
                <div class="mb-4 rounded-xl px-5 py-4 border-l-4 bg-red-50" style="border-color: #ef4444;">
                    <p class="text-sm font-semibold text-red-700">{{ $errors->first('folio') }}</p>
                </div>
            @endif

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-2 w-full" style="background-color: #0F4229;"></div>

                <div class="p-8 md:p-10">
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        Ingresa el folio que recibiste al completar tu solicitud de registro.
                        Puedes encontrarlo en el correo de confirmación que te enviamos.
                    </p>

                    <form method="GET" action="{{ route('aspirantes.resultado') }}">

                        <div class="mb-5">
                            <label for="folio"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                Número de folio <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="folio"
                                   name="folio"
                                   placeholder="UICM-2026-0001"
                                   required
                                   class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm tracking-widest font-mono uppercase focus:outline-none focus:ring-2 focus:border-transparent"
                                   style="--tw-ring-color: #0F4229;">
                            <p class="text-xs text-gray-400 mt-1.5">Ejemplo: UICM-2026-0001</p>
                        </div>

                        <button type="submit"
                                class="w-full py-3.5 rounded-xl text-white font-bold text-sm transition-colors duration-200 shadow-md"
                                style="background-color: #0F4229;"
                                onmouseover="this.style.backgroundColor='#0a2f1c'"
                                onmouseout="this.style.backgroundColor='#0F4229'">
                            Consultar estado
                        </button>

                    </form>

                    {{-- Separador --}}
                    <div class="flex items-center gap-3 my-6">
                        <div class="flex-1 h-px bg-gray-100"></div>
                        <span class="text-xs text-gray-400">¿Aún no tienes folio?</span>
                        <div class="flex-1 h-px bg-gray-100"></div>
                    </div>

                    <a href="{{ route('aspirantes.registro') }}"
                       class="flex items-center justify-center gap-2 w-full py-3 rounded-xl border-2 font-semibold text-sm transition-colors duration-200"
                       style="border-color: #0F4229; color: #0F4229;"
                       onmouseover="this.style.backgroundColor='#f0f9f4'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        Iniciar registro de aspirante
                    </a>
                </div>
            </div>

            {{-- Volver --}}
            <div class="text-center mt-6">
                <a href="{{ route('home') }}"
                   class="text-sm text-gray-400 hover:text-uicm-green transition-colors duration-200 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver al inicio
                </a>
            </div>

        </div>
    </div>
</section>

@endsection
