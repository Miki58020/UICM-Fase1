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
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white self-start sm:self-auto"
                              style="background-color: #EFAD5A;">
                            <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                            Pago pendiente
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-5">
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
                        <div class="bg-uicm-gray rounded-xl p-4">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Monto</p>
                            <p class="text-xl font-extrabold" style="color: #0F4229;">${{ number_format($monto, 0) }} MXN</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════
                 CARD: Payment Brick
            ══════════════════════════════════════ --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #D4AF37;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700">Selecciona tu método de pago</h3>
                </div>

                <div class="px-6 py-5">

                    {{-- Mensaje de error al procesar --}}
                    <div id="mp-error"
                         class="hidden mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="mp-error-msg">Ocurrió un error al procesar tu pago. Intenta de nuevo.</span>
                    </div>

                    {{-- Contenedor del brick --}}
                    <div id="paymentBrick_container"></div>

                </div>
            </div>

            {{-- Volver --}}
            <div class="flex justify-start">
                <a href="{{ route('aspirantes.resultado', ['folio' => $aspirante->folio]) }}"
                   class="group inline-flex items-center gap-3 px-4 py-3 rounded-xl
                          bg-white shadow-sm border border-gray-200
                          hover:border-gray-300 hover:shadow transition-all duration-150">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors duration-150"
                          style="background-color: #f0f9f4;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                    <span class="text-xs font-semibold text-gray-500 group-hover:text-gray-700 transition-colors duration-150">
                        Regresar
                    </span>
                </a>
            </div>

        </div>
    </div>
</section>

@push('scripts')
<audio id="mp-success-sound" src="{{ asset('sounds/mp-success.mp3') }}" preload="auto"></audio>
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
(async () => {
    const mp            = new MercadoPago('{{ $publicKey }}', { locale: 'es-MX' });
    const bricksBuilder = mp.bricks();

    const settings = {
        initialization: {
            amount:       {{ $monto }},
            preferenceId: '{{ $preferenceId }}',
        },
        customization: {
            paymentMethods: {
                ticket:       'all',
                bankTransfer: 'all',
                creditCard:   'all',
                debitCard:    'all',
                mercadoPago:  'all',
            },
        },
        callbacks: {
            onReady: () => {},

            onSubmit: ({ formData }) => {
                return new Promise((resolve, reject) => {
                    const errorBox = document.getElementById('mp-error');
                    errorBox.classList.add('hidden');

                    fetch('{{ route('aspirantes.pago.procesar') }}', {
                        method:  'POST',
                        headers: {
                            'Content-Type':  'application/json',
                            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ ...formData, folio: '{{ $aspirante->folio }}' }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.redirect_url) {
                            const sound = document.getElementById('mp-success-sound');
                            sound.play().catch(() => {});
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 1500);
                            resolve();
                        } else if (data.status === 'rejected') {
                            errorBox.classList.remove('hidden');
                            document.getElementById('mp-error-msg').textContent =
                                'Tu pago fue rechazado. Verifica los datos de tu tarjeta e intenta de nuevo.';
                            reject();
                        } else {
                            reject();
                        }
                    })
                    .catch(() => {
                        errorBox.classList.remove('hidden');
                        reject();
                    });
                });
            },

            onError: (error) => {
                console.error('MP Brick error:', error);
            },
        },
    };

    await bricksBuilder.create('payment', 'paymentBrick_container', settings);
})();
</script>
@endpush

@endsection
