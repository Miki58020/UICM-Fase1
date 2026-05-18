@extends('layouts.app')

@section('title', 'Configuración MercadoPago | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
<div class="container mx-auto px-4 lg:px-12 max-w-4xl">

    {{-- Cabecera --}}
    <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Administración</p>
        <h1 class="text-2xl font-extrabold text-gray-900">Configuración MercadoPago</h1>
        <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
    </div>

    {{-- ═══ CARD 1: CREDENCIALES ═══ --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Credenciales de API</h2>
            <button onclick="document.getElementById('modal-credenciales').classList.remove('hidden')"
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
                            {{ $config ? Str::limit($config->public_key, 30, '...') . substr($config->public_key, -6) : '—' }}
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-700">Access Token</td>
                        <td class="px-6 py-4 font-mono text-gray-600 text-xs">
                            {{ $config ? Str::limit($config->access_token, 20) . '••••••••••••' . substr($config->access_token, -6) : '—' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══ CARD 2: URLs DE RETORNO ═══ --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">URLs de retorno y webhook</h2>
            <button onclick="document.getElementById('modal-urls').classList.remove('hidden')"
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
                        <td class="px-6 py-4 font-mono text-gray-600 text-xs break-all">{{ $config?->back_url_success ?? '—' }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Pago pendiente</td>
                        <td class="px-6 py-4 font-mono text-gray-600 text-xs break-all">{{ $config?->back_url_pending ?? '—' }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Pago fallido</td>
                        <td class="px-6 py-4 font-mono text-gray-600 text-xs break-all">{{ $config?->back_url_failure ?? '—' }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Webhook</td>
                        <td class="px-6 py-4 font-mono text-gray-600 text-xs break-all">
                            {{ $config?->notification_url ?? '—' }}
                            @if(!$config?->notification_url)
                                <span class="ml-1 text-gray-400 font-sans font-normal">(opcional en sandbox)</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
</section>

{{-- ═══ MODAL CREDENCIALES ═══ --}}
<div id="modal-credenciales" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold" style="color: #0F4229;">Editar credenciales</h2>
                <p class="text-xs text-gray-400 mt-0.5">Los cambios aplican de inmediato a todos los pagos</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-credenciales').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.mercadopago.credenciales', $config->id) }}"
              class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Public Key <span class="text-red-400">*</span>
                </label>
                <input type="text" name="public_key"
                       value="{{ old('public_key', $config?->public_key) }}"
                       placeholder="TEST-xxxx o APP_USR-xxxx"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>

            <div x-data="{ showToken: false }">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Access Token <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <input :type="showToken ? 'text' : 'password'" name="access_token"
                           value="{{ old('access_token', $config?->access_token) }}"
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
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-credenciales').classList.add('hidden')"
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

{{-- ═══ MODAL URLs ═══ --}}
<div id="modal-urls" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold" style="color: #0F4229;">Editar URLs de retorno</h2>
                <p class="text-xs text-gray-400 mt-0.5">Rutas a las que MercadoPago redirige al usuario</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-urls').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.mercadopago.urls', $config->id) }}"
              class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Pago exitoso <span class="text-red-400">*</span>
                </label>
                <input type="url" name="back_url_success"
                       value="{{ old('back_url_success', $config?->back_url_success) }}"
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
                       value="{{ old('back_url_pending', $config?->back_url_pending) }}"
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
                       value="{{ old('back_url_failure', $config?->back_url_failure) }}"
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
                       value="{{ old('notification_url', $config?->notification_url) }}"
                       placeholder="https://tudominio.com/mp/webhook"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg font-mono focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modal-urls').classList.add('hidden')"
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

@push('scripts')
<script>
document.getElementById('modal-credenciales').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
document.getElementById('modal-urls').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endpush

@endsection
