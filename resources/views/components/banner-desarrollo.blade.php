{{-- Banner reutilizable para marcar secciones/funcionalidades en desarrollo. --}}
{{-- Para quitarlo cuando la funcionalidad esté lista, simplemente eliminar este <x-banner-desarrollo>. --}}
<div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-amber-50 flex items-start gap-3" style="border-color: #D4AF37;">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <div class="text-sm text-amber-800">
        <p class="font-semibold">En desarrollo</p>
        <div class="mt-0.5">{{ $slot }}</div>
    </div>
</div>
