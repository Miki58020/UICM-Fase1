<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('¿Olvidaste tu contraseña? Ingresa tu correo y un administrador te enviará una nueva contraseña.') }}
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3">
            <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
        </div>
    @endif

    @if (session('status_error'))
        <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-300 px-4 py-3">
            <p class="text-sm font-medium text-yellow-800">{{ session('status_error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Correo electrónico -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Enviar solicitud') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
