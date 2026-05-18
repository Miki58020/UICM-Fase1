@extends('layouts.app')

@section('title', 'Detalle de Aspirante | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        {{-- ── Navegación de migas ── --}}
        <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
            <a href="{{ route('admin.aspirantes.index') }}" class="hover:text-uicm-green transition-colors">Aspirantes</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-600">{{ $aspirante->folio }}</span>
        </nav>



        {{-- ── Encabezado del expediente ── --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                        Expediente de aspirante
                    </p>
                    <h1 class="text-xl font-extrabold text-gray-900">
                        {{ $aspirante->nombre_completo }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $aspirante->folio }}</p>
                </div>

                {{-- Badge estado --}}
                <div>
                    @if ($aspirante->estado === 'pendiente')
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-white"
                              style="background-color: #EFAD5A;">
                            <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                            Pendiente de validación
                        </span>
                    @elseif ($aspirante->estado === 'aprobado')
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-white"
                              style="background-color: #0F4229;">
                            <span class="w-2 h-2 rounded-full bg-white opacity-80 inline-block"></span>
                            Aprobado
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-gray-600 bg-gray-200">
                            <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                            Rechazado
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Línea de tiempo del proceso ── --}}
        @php
            $pago = $aspirante->pagos->last();

            if ($aspirante->estado === 'pendiente') {
                $pasoActual = 0;
            } elseif ($aspirante->estado === 'rechazado') {
                $pasoActual = -1;
            } elseif ($aspirante->estado === 'aprobado' && !$pago) {
                $pasoActual = 1;
            } elseif ($aspirante->estado === 'aprobado' && $pago?->estado === 'pendiente') {
                $pasoActual = 2;
            } elseif ($aspirante->estado === 'aprobado' && $pago?->estado === 'aprobado' && !$aspirante->alumno?->user_id) {
                $pasoActual = 3;
            } else {
                $pasoActual = 4;
            }

            $pasos = [
                ['label' => 'Expediente recibido',  'sub' => 'Pendiente de validar'],
                ['label' => 'Expediente aprobado',  'sub' => 'Esperando pago'],
                ['label' => 'Pago en revisión',     'sub' => 'Finanzas validando'],
                ['label' => 'Generando acceso',     'sub' => 'Credenciales pendientes'],
                ['label' => 'Inscrito',              'sub' => 'Proceso completado'],
            ];
        @endphp

        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="px-6 py-5">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5">Progreso del proceso de admisión</h3>

                @if($pasoActual === -1)
                    {{-- Estado rechazado --}}
                    <div class="flex items-center gap-3 py-2">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-gray-200">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-600">Expediente rechazado</p>
                            <p class="text-xs text-gray-400">El proceso no continuará para este aspirante</p>
                        </div>
                    </div>
                @else
                    {{-- VERSIÓN ESCRITORIO --}}
                    <div class="hidden md:flex items-start justify-between relative">
                        <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-200 z-0" style="margin: 0 2rem;"></div>

                        @foreach($pasos as $i => $paso)
                        <div class="flex flex-col items-center flex-1 relative z-10">
                            @if($i < $pasoActual)
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm mb-3"
                                     style="background-color: #0F4229;">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            @elseif($i === $pasoActual)
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm mb-3 ring-4 ring-orange-200"
                                     style="background-color: #EFAD5A;">
                                    <div class="w-2.5 h-2.5 rounded-full bg-white"></div>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mb-3 bg-gray-100 border-2 border-gray-200">
                                    <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                </div>
                            @endif

                            <p class="text-center text-xs font-semibold leading-tight px-1
                                {{ $i < $pasoActual ? 'text-uicm-green' : ($i === $pasoActual ? 'text-orange-500' : 'text-gray-400') }}">
                                {{ $paso['label'] }}
                            </p>
                            <p class="text-center text-xs leading-tight px-1 mt-0.5
                                {{ $i < $pasoActual ? 'text-green-400' : ($i === $pasoActual ? 'text-orange-400' : 'text-gray-300') }}">
                                {{ $paso['sub'] }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    {{-- VERSIÓN MÓVIL --}}
                    <div class="flex flex-col gap-0 md:hidden">
                        @foreach($pasos as $i => $paso)
                        <div class="flex gap-4 items-start">
                            <div class="flex flex-col items-center flex-shrink-0">
                                @if($i < $pasoActual)
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm"
                                         style="background-color: #0F4229;">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @elseif($i === $pasoActual)
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm ring-4 ring-orange-200"
                                         style="background-color: #EFAD5A;">
                                        <div class="w-2.5 h-2.5 rounded-full bg-white"></div>
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 border-2 border-gray-200">
                                        <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                    </div>
                                @endif
                                @if(!$loop->last)
                                <div class="w-0.5 flex-1 my-1"
                                     style="height: 28px; background-color: {{ $i < $pasoActual ? '#0F4229' : '#E5E7EB' }};"></div>
                                @endif
                            </div>

                            <div class="pb-5">
                                <p class="text-sm font-semibold
                                    {{ $i < $pasoActual ? 'text-uicm-green' : ($i === $pasoActual ? 'text-orange-500' : 'text-gray-400') }}">
                                    {{ $paso['label'] }}
                                </p>
                                <p class="text-xs
                                    {{ $i < $pasoActual ? 'text-green-400' : ($i === $pasoActual ? 'text-orange-400' : 'text-gray-300') }}">
                                    {{ $paso['sub'] }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
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
                            <dd class="text-sm font-semibold text-gray-800">{{ $aspirante->nombre_completo }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">CURP</dt>
                            <dd class="text-sm font-mono font-semibold text-gray-800 tracking-wide">{{ $aspirante->curp }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Fecha de nacimiento</dt>
                            <dd class="text-sm text-gray-800">
                                {{ \Carbon\Carbon::parse($aspirante->fecha_nacimiento)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Teléfono</dt>
                            <dd class="text-sm text-gray-800">{{ $aspirante->telefono }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Correo electrónico</dt>
                            <dd class="text-sm text-gray-800">{{ $aspirante->email }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Fecha de registro</dt>
                            <dd class="text-sm text-gray-800">
                                {{ $aspirante->created_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Programa solicitado</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $aspirante->programa->nombre ?? '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Generación</dt>
                            <dd class="text-sm font-bold" style="color: #0F4229;">{{ $aspirante->generacion }}</dd>
                        </div>

                    </dl>
                </div>
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
                        $nivelPrograma = $aspirante->programa->nivel ?? 'licenciatura';
                        $labelCert = match($nivelPrograma) {
                            'maestria'  => 'Título de ingeniería o licenciatura',
                            'doctorado' => 'Título de ingeniería o licenciatura',
                            default     => 'Certificado de bachillerato',
                        };
                        $documentos = [
                            ['nombre' => 'Acta de nacimiento',           'url' => $aspirante->acta_nacimiento_url],
                            ['nombre' => 'CURP',                         'url' => $aspirante->curp_url],
                            ['nombre' => $labelCert,                     'url' => $aspirante->certificado_url],
                            ['nombre' => 'Identificación oficial (INE)', 'url' => $aspirante->identificacion_url],
                            ['nombre' => 'Comprobante de domicilio',     'url' => $aspirante->comprobante_domicilio_url],
                            ['nombre' => 'Fotografía',                   'url' => $aspirante->foto_url],
                        ];
                        if (!empty($aspirante->titulo_url)) {
                            $documentos[] = ['nombre' => 'Título de maestría', 'url' => $aspirante->titulo_url];
                        }
                        $subidos = collect($documentos)->filter(fn($d) => !empty($d['url']))->count();
                    @endphp

                    @foreach ($documentos as $doc)
                    <li class="px-6 py-3.5 flex items-center justify-between gap-3">
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
                            <span class="text-xs text-gray-700 font-medium truncate">{{ $doc['nombre'] }}</span>
                        </div>

                        @if (!empty($doc['url']))
                            <button type="button"
                                    onclick="abrirVistaPrevia('{{ route('admin.archivo', ['path' => $doc['url']]) }}', '{{ $doc['nombre'] }}', '{{ pathinfo($doc['url'], PATHINFO_EXTENSION) }}')"
                                    class="flex-shrink-0 text-xs font-semibold transition-colors duration-150"
                                    style="color: #D4AF37;"
                                    onmouseover="this.style.color='#b8962e'"
                                    onmouseout="this.style.color='#D4AF37'">
                                Ver
                            </button>
                        @else
                            <span class="flex-shrink-0 text-xs text-gray-400 italic">Pendiente</span>
                        @endif
                    </li>
                    @endforeach

                </ul>

                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
                    <p class="text-xs text-gray-400">
                        {{ $subidos }} de {{ count($documentos) }} documentos recibidos
                    </p>
                </div>
            </div>

        </div>{{-- /grid --}}

        {{-- ── Observaciones si está rechazado ── --}}
        @if ($aspirante->estado === 'rechazado' && $aspirante->observaciones)
            <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-gray-50" style="border-color: #9ca3af;">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Motivo de rechazo</p>
                <p class="text-sm text-gray-700">{{ $aspirante->observaciones }}</p>
            </div>
        @endif

        {{-- ── Acciones principales ── --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Resolución del expediente</h2>
            </div>
            <div class="px-6 py-5 flex flex-col gap-4">

                @if ($aspirante->estado === 'pendiente')

                    <div class="flex flex-col sm:flex-row items-start gap-4">

                        {{-- Aprobar --}}
                        <form method="POST" action="{{ route('admin.aspirantes.aprobar', $aspirante->id) }}"
                              onsubmit="return confirm('¿Confirmas la aprobación del expediente de este aspirante?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2
                                           px-6 py-3 rounded-xl text-sm font-bold text-white
                                           transition-colors duration-200 shadow-sm"
                                    style="background-color: #0F4229;"
                                    onmouseover="this.style.backgroundColor='#0a2e1c'"
                                    onmouseout="this.style.backgroundColor='#0F4229'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Aprobar aspirante
                            </button>
                        </form>

                        {{-- Rechazar (toggle) --}}
                        <button type="button"
                                onclick="document.getElementById('form-rechazar').classList.toggle('hidden')"
                                class="inline-flex items-center justify-center gap-2
                                       px-6 py-3 rounded-xl text-sm font-bold text-white
                                       bg-gray-400 hover:bg-gray-500 transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Rechazar aspirante
                        </button>

                    </div>

                    {{-- Formulario de rechazo --}}
                    <div id="form-rechazar" class="hidden">
                        <form method="POST" action="{{ route('admin.aspirantes.rechazar', $aspirante->id) }}">
                            @csrf
                            @method('PATCH')
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                                Motivo del rechazo <span class="text-red-500">*</span>
                            </label>
                            <textarea name="observaciones" required maxlength="500" rows="3"
                                      placeholder="Describe el motivo por el que se rechaza la solicitud..."
                                      class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 resize-none mb-3"
                                      style="--tw-ring-color: #9ca3af;">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <p class="text-xs text-red-500 mb-2">{{ $message }}</p>
                            @enderror
                            <button type="submit"
                                    class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gray-500 hover:bg-gray-600 transition-colors shadow-sm">
                                Confirmar rechazo
                            </button>
                        </form>
                    </div>

                @else
                    <p class="text-sm text-gray-500 italic">
                        Este expediente ya fue procesado ({{ $aspirante->estado }}).
                    </p>
                @endif

                {{-- Volver a la lista --}}
                <div>
                    <a href="{{ route('admin.aspirantes.index') }}"
                       class="inline-flex items-center justify-center gap-2
                              px-6 py-3 rounded-xl text-sm font-bold border-2
                              transition-colors duration-200"
                       style="border-color: #0F4229; color: #0F4229;"
                       onmouseover="this.style.backgroundColor='#f0f9f4'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Volver a la lista
                    </a>
                </div>

            </div>
        </div>{{-- /acciones --}}

    </div>
</section>

{{-- ══════════════════════════════════════
     MODAL: Vista previa de documento
══════════════════════════════════════ --}}
<div id="modal-documento"
     class="fixed inset-0 z-50 hidden flex items-center justify-center p-4"
     style="background-color: rgba(0,0,0,0.6);"
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
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors duration-150"
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
const imagenes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

function abrirVistaPrevia(url, nombre, extension) {
    const ext = extension.toLowerCase();

    document.getElementById('modal-titulo').textContent   = nombre;
    document.getElementById('modal-link-externo').href    = url;

    const iframe = document.getElementById('modal-iframe');
    const img    = document.getElementById('modal-img');

    if (imagenes.includes(ext)) {
        iframe.classList.add('hidden');
        iframe.src = '';
        img.src    = url;
        img.classList.remove('hidden');
    } else {
        img.classList.add('hidden');
        img.src    = '';
        iframe.src = url;
        iframe.classList.remove('hidden');
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
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.getElementById('modal-documento').classList.add('hidden');
        document.getElementById('modal-iframe').src = '';
        document.getElementById('modal-img').src    = '';
        document.body.style.overflow = '';
    }
});
</script>
@endpush

@endsection
