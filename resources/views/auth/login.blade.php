@extends('layouts.app')

@section('title', 'Inicio de Sesión | UICM')

@section('content')

{{-- Alpine.js scope: controla la visibilidad del modal --}}
<section
    x-data="{ showModal: {{ session('status') || session('status_error') ? 'true' : 'false' }} }"
    class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">

    {{-- ============================================================
         CARD PRINCIPAL
    ============================================================ --}}
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg px-8 py-10">

        {{-- ── Encabezado ── --}}
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Logo UICM"
                 class="h-16 w-auto mx-auto mb-4">

            <h1 class="text-lg font-bold leading-snug" style="color: #0F4229;">
                Universidad Internacional Cuba México
            </h1>

            {{-- Línea decorativa dorada --}}
            <div class="w-16 h-0.5 mx-auto my-3" style="background-color: #D4AF37;"></div>

            <p class="text-sm text-gray-500">Sistema de Gestión Académica</p>
        </div>

        {{-- ── Formulario ── --}}
        <form method="POST" action="{{ route('login.procesar') }}">
            @csrf

            {{-- Correo --}}
            <div class="mb-5">
                <label for="email"
                       class="block text-sm font-medium text-gray-700 mb-1">
                    Correo
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="usuario@correo.com"
                    required
                    class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:border-transparent
                           transition-colors duration-150"
                    style="--tw-ring-color: #0F4229;"
                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.25)'"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>

            {{-- Contraseña --}}
            <div class="mb-7">
                <label for="password"
                       class="block text-sm font-medium text-gray-700 mb-1">
                    Contraseña
                </label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Ingresa tu contraseña"
                        required
                        class="w-full px-4 py-2.5 pr-11 text-sm border border-gray-300 rounded-lg
                               focus:outline-none transition-colors duration-150"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.25)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <button type="button"
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>


            {{-- Botón principal --}}
            <button
                type="submit"
                class="w-full py-2.5 px-4 text-sm font-semibold text-white rounded-lg
                       transition-colors duration-200"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2e1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
                Iniciar sesión
            </button>

            {{-- ¿Olvidaste tu contraseña? --}}
            <div class="text-center mt-4">
                <button
                    type="button"
                    @click="showModal = true"
                    class="text-sm font-medium transition-colors duration-150"
                    style="color: #0F4229;"
                    onmouseover="this.style.textDecoration='underline'"
                    onmouseout="this.style.textDecoration='none'">
                    ¿Olvidaste tu contraseña?
                </button>
            </div>

        </form>
    </div>{{-- /card --}}


    {{-- ============================================================
         MODAL — Recuperar contraseña
         Controlado por Alpine.js (x-show / x-transition)
    ============================================================ --}}
    <div
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm"
        @keydown.escape.window="showModal = false"
        x-cloak>

        {{-- Panel del modal --}}
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold" style="color: #0F4229;">
                    Recuperar contraseña
                </h2>
                <button
                    type="button"
                    @click="showModal = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors duration-150"
                    aria-label="Cerrar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <form method="POST" action="{{ route('password.email') }}" class="px-6 py-5">
                @csrf

                {{-- Feedback de éxito --}}
                @if(session('status'))
                    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3">
                        <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                    </div>
                @endif

                {{-- Feedback de límite de tiempo --}}
                @if(session('status_error'))
                    <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-300 px-4 py-3">
                        <p class="text-sm font-medium text-yellow-800">{{ session('status_error') }}</p>
                    </div>
                @endif

                <p class="text-sm text-gray-600 mb-4">
                    Ingresa tu correo institucional y el área administrativa te apoyará
                    con la recuperación.
                </p>

                <div class="mb-4">
                    <label for="email_recuperacion"
                           class="block text-sm font-medium text-gray-700 mb-1">
                        Correo institucional
                    </label>
                    <input
                        type="email"
                        id="email_recuperacion"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="usuario@uicm.edu.mx"
                        class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg
                               focus:outline-none transition-colors duration-150"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.25)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    @error('email')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-xs text-gray-400 mb-5">
                    La recuperación se gestionará directamente con el área administrativa.
                </p>

                {{-- Footer del modal --}}
                <div class="flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300
                               rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        Cerrar
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg
                               transition-colors duration-150"
                        style="background-color: #EFAD5A;"
                        onmouseover="this.style.backgroundColor='#e09a3a'"
                        onmouseout="this.style.backgroundColor='#EFAD5A'">
                        Enviar solicitud
                    </button>
                </div>

            </form>
        </div>{{-- /panel --}}
    </div>{{-- /modal overlay --}}

</section>

@endsection

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const iconEye = document.getElementById('icon-eye');
    const iconEyeOff = document.getElementById('icon-eye-off');
    if (input.type === 'password') {
        input.type = 'text';
        iconEye.classList.add('hidden');
        iconEyeOff.classList.remove('hidden');
    } else {
        input.type = 'password';
        iconEye.classList.remove('hidden');
        iconEyeOff.classList.add('hidden');
    }
}
</script>
@endpush