@extends('layouts.app')

@section('title', 'Consultar Estado de Inscripción | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen flex items-center py-16">
    <div class="container mx-auto px-4 sm:px-8 lg:px-24">

        <div class="max-w-lg mx-auto">

            {{-- Encabezado --}}
            <div class="text-center mb-8">
                <p class="text-uicm-gold text-xs font-bold uppercase tracking-widest mb-2">Admisiones</p>
                <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Consulta tu estado de inscripción</h1>
                <div class="mx-auto w-16 h-1 rounded-full" style="background-color: #D4AF37;"></div>
            </div>


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
                                   maxlength="14"
                                   required
                                   autocomplete="off"
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

@push('scripts')
<script>
    const TIMEOUT_MS = 30 * 60 * 1000; // 30 minutos

    sessionStorage.setItem('seguimiento_ts', Date.now());

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            const ts = parseInt(sessionStorage.getItem('seguimiento_ts') || '0');
            if (Date.now() - ts > TIMEOUT_MS) {
                document.getElementById('folio').value = '';
            }
            sessionStorage.setItem('seguimiento_ts', Date.now());
        }
    });

    const folioInput = document.getElementById('folio');

    folioInput.addEventListener('input', function (e) {
        let raw = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();

        let result = '';

        // Parte 1: UICM (4 letras)
        const p1 = raw.slice(0, 4);
        // Parte 2: año (4 dígitos)
        const p2 = raw.slice(4, 8);
        // Parte 3: número (4 dígitos)
        const p3 = raw.slice(8, 12);

        result = p1;
        if (raw.length > 4) result += '-' + p2;
        if (raw.length > 8) result += '-' + p3;

        this.value = result;
    });
</script>
@endpush

@endsection
