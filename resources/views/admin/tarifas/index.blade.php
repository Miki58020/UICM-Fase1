@extends('layouts.app')

@section('title', 'Tarifas de Inscripción | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
<div class="container mx-auto px-4 lg:px-12 max-w-3xl">

    {{-- Cabecera --}}
    <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
            Administración
        </p>
        <h1 class="text-2xl font-extrabold text-gray-900">Tarifas de Inscripción</h1>
        <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
    </div>

    {{-- Tabla de tarifas --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

        <div class="px-6 py-5">
            <p class="text-sm text-gray-500 mb-5">
                Configura el monto de inscripción por nivel académico. Los cambios aplican a nuevos pagos inmediatamente.
            </p>

            <div class="space-y-3">
                @foreach($tarifas as $tarifa)
                <div class="flex items-center justify-between gap-4 rounded-xl px-5 py-4 bg-uicm-gray">
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Nivel</p>
                        <p class="font-bold text-gray-900 capitalize">{{ $tarifa->nivel }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Monto actual</p>
                        <p class="text-xl font-extrabold" style="color: #0F4229;">${{ number_format($tarifa->monto, 0) }} MXN</p>
                    </div>
                    <button onclick="abrirModal({{ $tarifa->id }}, '{{ $tarifa->nivel }}', {{ $tarifa->monto }})"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200 flex-shrink-0"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
</section>

{{-- Modal de edición --}}
<div id="modal-editar" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

        <div class="px-6 py-5">
            <h2 class="text-base font-extrabold text-gray-900 mb-1">Editar tarifa</h2>
            <p id="modal-nivel" class="text-sm text-gray-500 mb-5 capitalize"></p>

            <form id="form-editar" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Nuevo monto (MXN)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                        <input type="number" name="monto" id="modal-monto"
                               min="1" max="99999" step="1" required
                               class="w-full pl-7 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:border-transparent"
                               style="focus-ring-color: #0F4229;">
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="cerrarModal()"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors duration-150">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-200"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModal(id, nivel, monto) {
    document.getElementById('modal-nivel').textContent = nivel;
    document.getElementById('modal-monto').value = monto;
    document.getElementById('form-editar').action = '/admin/tarifas/' + id;
    document.getElementById('modal-editar').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modal-editar').classList.add('hidden');
}

document.getElementById('modal-editar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
@endpush

@endsection
