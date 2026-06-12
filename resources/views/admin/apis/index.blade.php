@extends('layouts.app')

@section('title', 'APIs | UICM')

@section('content')

@php
$tabs = [
    'mercadopago' => [
        'label' => 'MercadoPago',
        'desc'  => 'Credenciales y URLs usadas para procesar los pagos en línea.',
        'color' => '#0F4229',
    ],
    'correo' => [
        'label' => 'Correo electrónico',
        'desc'  => 'Servidor SMTP y remitente usados para enviar notificaciones por correo.',
        'color' => '#D4AF37',
    ],
];

$mailerLabel = fn($m) => match($m) {
    'smtp'     => 'SMTP',
    'sendmail' => 'Sendmail (servidor local)',
    'log'      => 'Log (no se envían, solo se registran)',
    default    => $m ? ucfirst($m) : '—',
};

$tabActivo = $tabInicial;
if ($errors->has('mailer') || $errors->has('host') || $errors->has('port') || $errors->has('username') || $errors->has('password') || $errors->has('from_address') || $errors->has('from_name')) {
    $tabActivo = 'correo';
} elseif ($errors->has('public_key') || $errors->has('access_token') || $errors->has('back_url_success') || $errors->has('back_url_pending') || $errors->has('back_url_failure') || $errors->has('notification_url')) {
    $tabActivo = 'mercadopago';
}
@endphp

<section class="bg-uicm-gray min-h-screen py-12 px-4">
<div class="container mx-auto px-4 lg:px-12 max-w-4xl">

    {{-- Cabecera --}}
    <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Administración</p>
        <h1 class="text-2xl font-extrabold text-gray-900">APIs y servicios externos</h1>
        <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        <p class="text-sm text-gray-500 mt-3 max-w-2xl">
            Configura aquí las credenciales de los servicios externos. Los cambios aplican de inmediato,
            sin necesidad de modificar código ni volver a desplegar el proyecto.
        </p>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: '{{ $tabActivo }}' }">

        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($tabs as $key => $info)
            <button @click="tab = '{{ $key }}'"
                    :style="tab === '{{ $key }}' ? 'background-color:{{ $info['color'] }};' : ''"
                    :class="tab === '{{ $key }}'
                        ? 'text-white shadow-sm'
                        : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                    class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-150">
                {{ $info['label'] }}
            </button>
            @endforeach
        </div>

        {{-- ═══════════════ TAB: MERCADOPAGO ═══════════════ --}}
        <div x-show="tab === 'mercadopago'"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            <p class="text-sm text-gray-500 mb-5">{{ $tabs['mercadopago']['desc'] }}</p>

            {{-- CARD: CREDENCIALES --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Credenciales de API</h2>
                    <button onclick="document.getElementById('modal-mp-credenciales').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200"
                            style="background-color: #D4AF37;"
                            onmouseover="this.style.backgroundColor='#b8962e'"
                            onmouseout="this.style.backgroundColor='#D4AF37'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Campo</th>
                                <th class="px-6 py-3">Valor actual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700">Public Key</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs">
                                    {{ $configMercadopago ? Str::limit($configMercadopago->public_key, 30, '...') . substr($configMercadopago->public_key, -6) : '—' }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700">Access Token</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs">
                                    @php $accessTokenActual = $configMercadopago?->accessTokenSeguro(); @endphp
                                    {{ $accessTokenActual ? Str::limit($accessTokenActual, 20) . '••••••••••••' . substr($accessTokenActual, -6) : '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CARD: URLs DE RETORNO --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">URLs de retorno y webhook</h2>
                    <button onclick="document.getElementById('modal-mp-urls').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200"
                            style="background-color: #D4AF37;"
                            onmouseover="this.style.backgroundColor='#b8962e'"
                            onmouseout="this.style.backgroundColor='#D4AF37'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Campo</th>
                                <th class="px-6 py-3">URL configurada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Pago exitoso</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs break-all">{{ $configMercadopago?->back_url_success ?? '—' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Pago pendiente</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs break-all">{{ $configMercadopago?->back_url_pending ?? '—' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Pago fallido</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs break-all">{{ $configMercadopago?->back_url_failure ?? '—' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Webhook</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs break-all">
                                    {{ $configMercadopago?->notification_url ?? '—' }}
                                    @if(!$configMercadopago?->notification_url)
                                        <span class="ml-1 text-gray-400 font-sans font-normal">(opcional en sandbox)</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ═══════════════ TAB: CORREO ═══════════════ --}}
        <div x-show="tab === 'correo'"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            <p class="text-sm text-gray-500 mb-5">{{ $tabs['correo']['desc'] }}</p>

            {{-- CARD: CONEXIÓN --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Servidor de envío</h2>
                    <button onclick="document.getElementById('modal-correo-conexion').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200"
                            style="background-color: #D4AF37;"
                            onmouseover="this.style.backgroundColor='#b8962e'"
                            onmouseout="this.style.backgroundColor='#D4AF37'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Campo</th>
                                <th class="px-6 py-3">Valor actual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Driver</td>
                                <td class="px-6 py-4 text-gray-600 text-xs">{{ $mailerLabel($configCorreo?->mailer) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Servidor (host)</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs">{{ $configCorreo?->host ?? '—' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Puerto</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs">{{ $configCorreo?->port ?? '—' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Usuario</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs">{{ $configCorreo?->username ?: '—' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Contraseña</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs">
                                    @php $passwordActual = $configCorreo?->passwordSeguro(); @endphp
                                    {{ $passwordActual ? '••••••••••••' . substr($passwordActual, -4) : '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CARD: REMITENTE --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Remitente de los correos</h2>
                    <button onclick="document.getElementById('modal-correo-remitente').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200"
                            style="background-color: #D4AF37;"
                            onmouseover="this.style.backgroundColor='#b8962e'"
                            onmouseout="this.style.backgroundColor='#D4AF37'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Campo</th>
                                <th class="px-6 py-3">Valor actual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Correo remitente</td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-xs">{{ $configCorreo?->from_address ?? '—' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Nombre mostrado</td>
                                <td class="px-6 py-4 text-gray-600 text-xs">{{ $configCorreo?->from_name ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
</section>

{{-- ═══════════════════════════════════════════════ --}}
{{-- MODALES                                          --}}
{{-- ═══════════════════════════════════════════════ --}}

{{-- ═══ MODAL: MP CREDENCIALES ═══ --}}
<div id="modal-mp-credenciales" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold" style="color: #0F4229;">Editar credenciales</h2>
                <p class="text-xs text-gray-400 mt-0.5">Los cambios aplican de inmediato a todos los pagos</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-mp-credenciales').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.apis.mercadopago.credenciales', $configMercadopago->id) }}"
              class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Public Key <span class="text-red-400">*</span>
                </label>
                <input type="text" name="public_key"
                       value="{{ old('public_key', $configMercadopago?->public_key) }}"
                       placeholder="TEST-xxxx o APP_USR-xxxx"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                <p class="text-xs text-gray-400 mt-1">Clave pública generada en tu cuenta de MercadoPago Developers.</p>
            </div>

            <div x-data="{ showToken: false }">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Access Token <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <input :type="showToken ? 'text' : 'password'" name="access_token"
                           value="{{ old('access_token', $configMercadopago?->accessTokenSeguro()) }}"
                           placeholder="TEST-xxxx o APP_USR-xxxx"
                           class="w-full px-3 py-2 pr-10 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <button type="button" @click="showToken = !showToken"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg x-show="!showToken" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showToken" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Token privado de la cuenta. Nunca lo compartas fuera del panel.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-mp-credenciales').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-bold text-white rounded-lg transition-colors duration-200 shadow-sm"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Guardar credenciales
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ MODAL: MP URLs ═══ --}}
<div id="modal-mp-urls" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 py-8 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden my-auto">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold" style="color: #0F4229;">Editar URLs de retorno</h2>
                <p class="text-xs text-gray-400 mt-0.5">Rutas a las que MercadoPago redirige al usuario</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-mp-urls').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.apis.mercadopago.urls', $configMercadopago->id) }}"
              class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Pago exitoso <span class="text-red-400">*</span>
                </label>
                <input type="url" name="back_url_success"
                       value="{{ old('back_url_success', $configMercadopago?->back_url_success) }}"
                       placeholder="https://tudominio.com/aspirante/pago/retorno"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Pago pendiente <span class="text-red-400">*</span>
                </label>
                <input type="url" name="back_url_pending"
                       value="{{ old('back_url_pending', $configMercadopago?->back_url_pending) }}"
                       placeholder="https://tudominio.com/aspirante/pago/retorno"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Pago fallido <span class="text-red-400">*</span>
                </label>
                <input type="url" name="back_url_failure"
                       value="{{ old('back_url_failure', $configMercadopago?->back_url_failure) }}"
                       placeholder="https://tudominio.com/aspirante/pago/retorno"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Webhook
                    <span class="normal-case font-normal text-gray-400 ml-1">— opcional en sandbox</span>
                </label>
                <input type="url" name="notification_url"
                       value="{{ old('notification_url', $configMercadopago?->notification_url) }}"
                       placeholder="https://tudominio.com/mp/webhook"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-mp-urls').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-bold text-white rounded-lg transition-colors duration-200 shadow-sm"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Guardar URLs
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ MODAL: CORREO CONEXIÓN ═══ --}}
<div id="modal-correo-conexion" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 py-8 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden my-auto"
         x-data="{ mailer: '{{ old('mailer', $configCorreo?->mailer ?? 'smtp') }}' }">
        <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold" style="color: #0F4229;">Editar servidor de envío</h2>
                <p class="text-xs text-gray-400 mt-0.5">Define cómo se entregan los correos del sistema</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-correo-conexion').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.apis.correo.conexion', $configCorreo->id) }}"
              class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Driver de envío <span class="text-red-400">*</span>
                </label>
                <select name="mailer" x-model="mailer"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="smtp">SMTP (Mailtrap, Gmail, servidor de tu hosting)</option>
                    <option value="sendmail">Sendmail (servidor local, p. ej. cPanel)</option>
                    <option value="log">Log (no envía, solo registra en logs)</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">En producción con cPanel normalmente se usa SMTP con los datos del correo del dominio, o Sendmail si el hosting lo ofrece sin credenciales.</p>
            </div>

            <div x-show="mailer === 'smtp'" x-cloak class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Servidor (host) <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="host"
                           value="{{ old('host', $configCorreo?->host) }}"
                           placeholder="sandbox.smtp.mailtrap.io o mail.tudominio.com"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Puerto <span class="text-red-400">*</span>
                    </label>
                    <input type="number" name="port"
                           value="{{ old('port', $configCorreo?->port) }}"
                           placeholder="2525, 587 o 465"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <p class="text-xs text-gray-400 mt-1">El cifrado se ajusta automáticamente según el puerto (465 usa SSL, los demás TLS/STARTTLS).</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Usuario
                    </label>
                    <input type="text" name="username"
                           value="{{ old('username', $configCorreo?->username) }}"
                           placeholder="usuario del servidor SMTP"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>

                <div x-data="{ showPass: false }">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Contraseña
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password"
                               value="{{ old('password', $configCorreo?->passwordSeguro()) }}"
                               placeholder="contraseña del servidor SMTP"
                               class="w-full px-3 py-2 pr-10 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Se guarda tal cual para autenticarse contra el servidor SMTP.</p>
                </div>
            </div>

            <div x-show="mailer !== 'smtp'" x-cloak>
                <p class="text-xs text-gray-400 bg-gray-50 border border-gray-100 rounded-lg px-3 py-2">
                    Este driver no requiere servidor, usuario ni contraseña: usa el servicio de correo del propio hosting.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-correo-conexion').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-bold text-white rounded-lg transition-colors duration-200 shadow-sm"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Guardar conexión
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ MODAL: CORREO REMITENTE ═══ --}}
<div id="modal-correo-remitente" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold" style="color: #0F4229;">Editar remitente</h2>
                <p class="text-xs text-gray-400 mt-0.5">Así verán los destinatarios el correo en su bandeja de entrada</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-correo-remitente').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.apis.correo.remitente', $configCorreo->id) }}"
              class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Correo remitente <span class="text-red-400">*</span>
                </label>
                <input type="email" name="from_address"
                       value="{{ old('from_address', $configCorreo?->from_address) }}"
                       placeholder="noreply@tudominio.com"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                <p class="text-xs text-gray-400 mt-1">Dirección desde la que salen todos los correos del sistema.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Nombre mostrado <span class="text-red-400">*</span>
                </label>
                <input type="text" name="from_name"
                       value="{{ old('from_name', $configCorreo?->from_name) }}"
                       placeholder="UICM"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                <p class="text-xs text-gray-400 mt-1">Nombre que acompaña al correo remitente, p. ej. "UICM &lt;noreply@tudominio.com&gt;".</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-correo-remitente').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-bold text-white rounded-lg transition-colors duration-200 shadow-sm"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Guardar remitente
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
@if($errors->any())
    @if($errors->has('public_key') || $errors->has('access_token'))
        document.getElementById('modal-mp-credenciales').classList.remove('hidden');
    @elseif($errors->has('back_url_success') || $errors->has('back_url_pending') || $errors->has('back_url_failure') || $errors->has('notification_url'))
        document.getElementById('modal-mp-urls').classList.remove('hidden');
    @elseif($errors->has('mailer') || $errors->has('host') || $errors->has('port') || $errors->has('username') || $errors->has('password'))
        document.getElementById('modal-correo-conexion').classList.remove('hidden');
    @elseif($errors->has('from_address') || $errors->has('from_name'))
        document.getElementById('modal-correo-remitente').classList.remove('hidden');
    @endif
@endif
</script>
@endpush

@endsection
