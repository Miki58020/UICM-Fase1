@extends('layouts.app')

@section('title', 'Expediente de Alumno | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        {{-- ── Encabezado del expediente ── --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                        Expediente de alumno
                    </p>
                    <h1 class="text-2xl font-extrabold text-gray-900">
                        {{ $alumno->nombre_completo }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $alumno->matricula }}</p>
                </div>

                @php
                    $colorEstado = match($alumno->estado) {
                        'activo'   => '#0F4229',
                        'inactivo' => '#EFAD5A',
                        'baja'     => '#dc2626',
                        default    => '#9ca3af',
                    };
                @endphp
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-white capitalize"
                      style="background-color: {{ $colorEstado }};">
                    <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                    {{ $alumno->estado }}
                </span>
            </div>
        </div>

        {{-- ── Dos columnas: datos + documentos ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">

            {{-- Columna datos personales (3/5) --}}
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Datos personales</h2>
                </div>

                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Nombre completo</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $alumno->nombre_completo }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Matrícula</dt>
                            <dd class="text-sm font-mono font-semibold text-gray-800 tracking-wide">{{ $alumno->matricula }}</dd>
                        </div>

                        @if($alumno->curp)
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">CURP</dt>
                            <dd class="text-sm font-mono font-semibold text-gray-800 tracking-wide">{{ $alumno->curp }}</dd>
                        </div>
                        @endif

                        @if($alumno->fecha_nacimiento)
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Fecha de nacimiento</dt>
                            <dd class="text-sm text-gray-800">
                                {{ $alumno->fecha_nacimiento->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                            </dd>
                        </div>
                        @endif

                        @if($alumno->telefono)
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Teléfono</dt>
                            <dd class="text-sm text-gray-800">{{ $alumno->telefono }}</dd>
                        </div>
                        @endif

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Correo electrónico</dt>
                            <dd class="text-sm text-gray-800">{{ $alumno->user?->email ?? $alumno->email }}</dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Programa</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $alumno->programa->nombre ?? '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Cuatrimestre actual</dt>
                            <dd class="text-sm font-bold" style="color: #0F4229;">{{ $alumno->cuatrimestre_actual ?? '—' }}</dd>
                        </div>

                        @if($alumno->migrado)
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Origen</dt>
                            <dd class="text-sm text-gray-800">Alumno migrado</dd>
                        </div>
                        @endif

                    </dl>
                </div>

                {{-- ═══ INICIO BLOQUE OPCIONAL: referencia a solicitud de aspirante ═══
                     Quitar este <div> completo si no se quiere mantener el link hacia
                     el expediente de aspirante original. No depende de nada más abajo. --}}
                @if($alumno->aspirante)
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Este alumno viene de una solicitud de aspirante (folio <span class="font-mono">{{ $alumno->aspirante->folio }}</span>). Puedes cotejar ahí los documentos que subió durante su registro.
                    </p>
                    <a href="{{ route('admin.aspirantes.show', $alumno->aspirante) }}"
                       class="flex-shrink-0 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors duration-150"
                       style="border-color: #0F4229; color: #0F4229;"
                       onmouseover="this.style.backgroundColor='#f0f9f4'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        Ver solicitud de aspirante
                    </a>
                </div>
                @endif
                {{-- ═══ FIN BLOQUE OPCIONAL ═══ --}}
            </div>

            {{-- Columna documentos (2/5) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                 a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19
                                 a2 2 0 01-2 2z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Documentos</h2>
                </div>

                <ul class="divide-y divide-gray-100">
                    @php
                        $subidos = $items->filter(fn($i) => $i['documento'])->count();
                    @endphp

                    @foreach ($items as $item)
                    @php
                        $doc = $item['documento'];
                        $estado = !$doc ? 'sin_subir' : ($doc->estaVencido() ? 'vencido' : ($doc->porVencer() ? 'por_vencer' : 'vigente'));
                        $badge = [
                            'sin_subir'  => ['#9ca3af', 'Sin subir'],
                            'vigente'    => ['#0F4229', 'Vigente'],
                            'por_vencer' => ['#EFAD5A', 'Próximo a vencer'],
                            'vencido'    => ['#dc2626', 'Vencido'],
                        ][$estado];
                    @endphp
                    <li class="px-6 py-3.5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="flex-shrink-0 w-7 h-7 rounded flex items-center justify-center"
                                     style="background-color: #f0f9f4;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                         style="color: #0F4229;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414
                                                 A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-700 font-medium truncate">{{ $item['label'] }}</span>
                            </div>

                            @if ($doc)
                                <button type="button"
                                        onclick="abrirVistaPrevia('{{ route('admin.archivo', ['path' => $doc->archivo_path]) }}', '{{ $item['label'] }}', '{{ pathinfo($doc->archivo_path, PATHINFO_EXTENSION) }}')"
                                        class="flex-shrink-0 text-xs font-semibold transition-colors duration-150"
                                        style="color: #D4AF37;"
                                        onmouseover="this.style.color='#b8962e'"
                                        onmouseout="this.style.color='#D4AF37'">
                                    Ver
                                </button>
                            @else
                                <span class="flex-shrink-0 text-xs text-gray-400 italic">Sin subir</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between mt-1.5 pl-9">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold text-white"
                                  style="background-color: {{ $badge[0] }};">
                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                {{ $badge[1] }}
                            </span>
                            @if($doc?->fecha_vigencia)
                            <span class="text-xs text-gray-400">Vigente hasta {{ $doc->fecha_vigencia->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>

                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green">
                        {{ $subidos }} de {{ $items->count() }} documentos subidos
                    </span>
                </div>
            </div>

        </div>{{-- /grid --}}

        {{-- ── Volver a la lista ── --}}
        <div>
            <a href="{{ route('admin.expedientes.index') }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-uicm-green transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al listado
            </a>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════
     MODAL: Vista previa de documento
══════════════════════════════════════ --}}
<div id="modal-documento"
     class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     onclick="cerrarVistaPrevia(event)">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-full sm:max-w-4xl flex flex-col overflow-hidden"
         style="max-height: 90vh;"
         onclick="event.stopPropagation()">

        {{-- Encabezado del modal --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4 flex-shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded flex items-center justify-center flex-shrink-0"
                     style="background-color: #f0f9f4;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414
                                 A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 id="modal-titulo" class="text-sm font-semibold text-gray-800 truncate"></h3>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a id="modal-link-externo" href="#" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold border transition-colors duration-150"
                   style="border-color: #0F4229; color: #0F4229;"
                   onmouseover="this.style.backgroundColor='#f0f9f4'"
                   onmouseout="this.style.backgroundColor='transparent'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4
                                 M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Abrir en pestaña
                </a>
                <button type="button" onclick="cerrarVistaPrevia()"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400
                               hover:text-gray-600 hover:bg-gray-100 transition-colors duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Cuerpo del modal --}}
        <div class="flex-1 overflow-auto bg-gray-50 flex items-center justify-center" style="min-height: 300px;">
            <iframe id="modal-iframe"
                    class="hidden w-full border-0"
                    style="height: 70vh;"
                    src=""></iframe>
            <img id="modal-img"
                 class="hidden max-w-full max-h-full object-contain p-4"
                 style="max-height: 70vh;"
                 src="" alt="">
        </div>

    </div>
</div>

@push('scripts')
<script>
const imagenesExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
let _blobUrlExpediente = null;

function _revocarBlobExpediente() {
    if (_blobUrlExpediente) { URL.revokeObjectURL(_blobUrlExpediente); _blobUrlExpediente = null; }
}

async function abrirVistaPrevia(url, nombre, extension) {
    const ext    = extension.toLowerCase();
    const iframe = document.getElementById('modal-iframe');
    const img    = document.getElementById('modal-img');
    const link   = document.getElementById('modal-link-externo');

    document.getElementById('modal-titulo').textContent = nombre;

    iframe.classList.add('hidden'); iframe.src = '';
    img.classList.add('hidden');    img.src    = '';
    _revocarBlobExpediente();

    try {
        const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const blob = await res.blob();
        _blobUrlExpediente = URL.createObjectURL(blob);
        link.href  = _blobUrlExpediente;

        if (imagenesExt.includes(ext)) {
            img.src = _blobUrlExpediente;
            img.classList.remove('hidden');
        } else {
            iframe.src = _blobUrlExpediente;
            iframe.classList.remove('hidden');
        }
    } catch (e) {
        link.href = url;
        if (imagenesExt.includes(ext)) {
            img.src = url; img.classList.remove('hidden');
        } else {
            iframe.src = url; iframe.classList.remove('hidden');
        }
    }

    document.getElementById('modal-documento').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarVistaPrevia(event) {
    if (event && event.target !== document.getElementById('modal-documento')) return;

    document.getElementById('modal-documento').classList.add('hidden');
    document.getElementById('modal-iframe').src = '';
    document.getElementById('modal-img').src    = '';
    document.body.style.overflow = '';
    _revocarBlobExpediente();
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.getElementById('modal-documento').classList.add('hidden');
        document.getElementById('modal-iframe').src = '';
        document.getElementById('modal-img').src    = '';
        document.body.style.overflow = '';
        _revocarBlobExpediente();
    }
});
</script>
@endpush

@endsection
