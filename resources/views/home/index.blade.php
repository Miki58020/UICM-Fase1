@extends('layouts.app')

@section('title', 'Universidad Internacional Cuba México | Inicio')

@section('content')

    {{-- ===== HERO SECTION ===== --}}
    <section id="inicio" class="relative overflow-hidden text-white" style="min-height: 88vh;">

        {{-- Imagen de fondo con degradado --}}
        <div class="absolute inset-0">
            <img src="{{ $heroImagen }}" alt="Fondo UICM"
                 class="w-full h-full object-cover object-center">
            {{-- Degradado encima de la imagen: izquierda sólido verde, derecha transparente --}}
            <div class="absolute inset-0"
                 style="background: linear-gradient(to right, rgba(15,66,41,0.97) 0%, rgba(15,66,41,0.85) 50%, rgba(15,66,41,0.45) 100%);"></div>
        </div>

        {{-- Overlay patrón decorativo sutil --}}
        <div class="absolute inset-0 opacity-5"
             style="background: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')">
        </div>

        <div class="container mx-auto px-4 sm:px-8 lg:px-12 py-16 md:py-24 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                {{-- Texto principal --}}
                <div class="lg:col-span-7">
                    <p class="text-green-300 text-sm font-semibold tracking-widest uppercase mb-3">
                        Universidad Internacional Cuba México
                    </p>
                    <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-5">
                        Educación superior con enfoque internacional, tecnológico y humano.
                    </h1>
                    <p class="text-green-100 text-lg mb-8 max-w-xl">
                        La UICM combina un modelo académico integral con una plataforma digital
                        que acompaña al estudiante desde su ingreso hasta la conclusión de sus estudios.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                        <a href="#oferta"
                           class="inline-flex items-center justify-center px-6 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-uicm-green transition-colors duration-200">
                            Consulta la oferta educativa
                        </a>
                    </div>
                </div>

                {{-- Panel lateral — "Lo que distingue a la UICM" con fondo blanco --}}
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-2xl">
                        <h5 class="font-bold text-lg mb-5 text-uicm-green">Lo que distingue a la UICM</h5>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <span class="mt-1.5 flex-shrink-0 w-2.5 h-2.5 rounded-full bg-uicm-gold"></span>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    Programas académicos diseñados para responder a los retos actuales del entorno profesional.
                                </p>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-1.5 flex-shrink-0 w-2.5 h-2.5 rounded-full bg-uicm-gold"></span>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    Acompañamiento académico, tutorías y seguimiento del desempeño del estudiante.
                                </p>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-1.5 flex-shrink-0 w-2.5 h-2.5 rounded-full bg-uicm-gold"></span>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    Procesos institucionales claros: admisión, reinscripción, evaluación y titulación.
                                </p>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-1.5 flex-shrink-0 w-2.5 h-2.5 rounded-full bg-uicm-gold"></span>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    Uso de herramientas digitales para la gestión académica y administrativa.
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>


    </section>

    {{-- ===== CIFRAS ===== --}}
    <section class="bg-uicm-gray py-12">
        <div class="container mx-auto px-4 sm:px-8 lg:px-12">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach([
                    ['nivel' => 'licenciatura', 'label' => 'Licenciaturas'],
                    ['nivel' => 'maestria',     'label' => 'Maestrías'],
                    ['nivel' => 'doctorado',    'label' => 'Doctorado'],
                ] as $item)
                @if(($conteoProgramas[$item['nivel']] ?? 0) > 0)
                <div class="bg-white rounded-xl shadow-sm p-6 text-center hover:shadow-md transition-shadow duration-200">
                    <div class="text-4xl font-extrabold text-uicm-green mb-1">{{ $conteoProgramas[$item['nivel']] }}</div>
                    <div class="text-gray-500 text-sm font-medium">{{ $item['label'] }}</div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== SOBRE LA UICM ===== --}}
    <section class="bg-white py-16">
        <div class="container mx-auto px-4 sm:px-8 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <p class="text-uicm-gold text-xs font-bold uppercase tracking-widest mb-2">Nuestra institución</p>
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-5">Una universidad con enfoque integral</h2>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        La Universidad Internacional Cuba México impulsa la formación de profesionales
                        capaces de responder a las necesidades de su entorno, a partir de un modelo
                        educativo que integra conocimientos, habilidades y valores.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Desde el proceso de admisión hasta la titulación, la UICM estructura
                        sus servicios académicos y administrativos mediante procedimientos claros,
                        apoyados por una plataforma institucional que organiza la información
                        y facilita la toma de decisiones.
                    </p>
                </div>

                {{-- Timeline --}}
                <div class="space-y-6">
                    @foreach([
                        ['Admisión y bienvenida', 'Procesos definidos de ingreso, inducción y acompañamiento para la integración del alumno a la vida universitaria.'],
                        ['Trayectoria académica', 'Seguimiento de materias, créditos, desempeño académico y regularización a través de la plataforma institucional.'],
                        ['Cierre de estudios', 'Gestión de procesos de titulación, egreso y vinculación con el ámbito profesional.'],
                    ] as $step)
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 flex flex-col items-center">
                            <div class="w-4 h-4 rounded-full border-4 border-uicm-green bg-white mt-1"></div>
                            @if(!$loop->last)
                            <div class="w-0.5 h-full bg-green-200 mt-1"></div>
                            @endif
                        </div>
                        <div class="pb-4">
                            <h6 class="font-bold text-gray-800 mb-1">{{ $step[0] }}</h6>
                            <p class="text-gray-500 text-sm leading-relaxed">{{ $step[1] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ===== ÁREAS DE FORMACIÓN ===== --}}
    <section id="oferta" class="bg-uicm-gray py-16">
        <div class="container mx-auto px-4 sm:px-8 lg:px-12">
            <div class="text-center mb-10">
                <p class="text-uicm-gold text-xs font-bold uppercase tracking-widest mb-2">Programas</p>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Áreas de formación</h2>
                <p class="text-gray-500">Programas académicos estructurados en distintos niveles educativos.</p>
            </div>

            <div class="grid gap-6" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr))">
                @php
                $nivelesConfig = [
                    'licenciatura' => ['label' => 'Licenciaturas', 'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
                    'maestria'     => ['label' => 'Maestrías',     'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                    'doctorado'    => ['label' => 'Doctorado',     'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                ];
                @endphp
                @foreach($nivelesConfig as $nivel => $cfg)
                @if(isset($ofertaHome[$nivel]) && $ofertaHome[$nivel]->count())
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 border-t-4 border-uicm-gold">
                    <div class="p-6">
                        <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-uicm-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cfg['icon'] }}"/>
                            </svg>
                        </div>
                        <h5 class="font-bold text-gray-800 text-lg mb-2">{{ $cfg['label'] }}</h5>
                        <ul class="space-y-1">
                            @foreach($ofertaHome[$nivel] as $prog)
                            <li class="flex items-center gap-2 text-sm text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-uicm-green flex-shrink-0"></span>
                                {{ str_replace(['Licenciatura en ', 'Maestría en ', 'Doctorado en '], '', $prog->nombre) }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('oferta-educativa') }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-uicm-green hover:text-green-800 transition-colors">
                    Ver oferta educativa completa
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ===== EVENTOS — CARRUSEL (solo si hay imágenes en el CMS) ===== --}}
    @if($imagenes->isNotEmpty())
    <section class="bg-white py-16">
        <div class="container mx-auto px-4 sm:px-8 lg:px-12">
            <div class="text-center mb-10">
                <p class="text-uicm-gold text-xs font-bold uppercase tracking-widest mb-2">Galería</p>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Eventos y vida universitaria</h2>
                <p class="text-gray-500">Momentos que hacen grande a nuestra comunidad.</p>
            </div>

            {{-- Carrusel --}}
            <div class="relative overflow-hidden rounded-2xl shadow-lg" id="carrusel">
                {{-- Track --}}
                <div class="flex transition-transform duration-500 ease-in-out" id="carrusel-track">
                    @foreach($imagenes as $img)
                    <div class="min-w-full">
                        <img src="{{ $img->url }}"
                             alt="Evento UICM"
                             class="w-full object-cover"
                             style="height: 420px; object-position: center;">
                    </div>
                    @endforeach
                </div>

                {{-- Botón anterior --}}
                <button id="prev-btn"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 text-uicm-green rounded-full w-10 h-10 flex items-center justify-center shadow-md transition-all duration-200 focus:outline-none"
                        aria-label="Anterior">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Botón siguiente --}}
                <button id="next-btn"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 text-uicm-green rounded-full w-10 h-10 flex items-center justify-center shadow-md transition-all duration-200 focus:outline-none"
                        aria-label="Siguiente">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                {{-- Indicadores (dots) --}}
                <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2" id="carrusel-dots">
                    @for($i = 0; $i < $imagenes->count(); $i++)
                    <button class="w-2.5 h-2.5 rounded-full transition-all duration-200 carrusel-dot {{ $i === 0 ? 'bg-white scale-125' : 'bg-white bg-opacity-50' }}"
                            data-index="{{ $i }}"
                            aria-label="Slide {{ $i + 1 }}"></button>
                    @endfor
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ===== CONVOCATORIA / CTA ===== --}}
    @if($periodoActivo)
    <section class="bg-uicm-gray py-10">
        <div class="container mx-auto px-4 sm:px-8 lg:px-12">
            <div class="rounded-xl p-6 md:p-8 border-l-4 border-uicm-orange bg-orange-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-uicm-green-soft text-uicm-green">
                            <span class="w-1.5 h-1.5 rounded-full bg-uicm-green"></span> Convocatoria abierta
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $periodoActivo->label }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        El proceso de admisión para el periodo <strong>{{ $periodoActivo->nombre }}</strong>
                        está disponible del {{ $periodoActivo->fecha_inicio_registro->format('d/m/Y') }}
                        al {{ $periodoActivo->fecha_fin_registro->format('d/m/Y') }}.
                        Asegúrate de tener toda tu documentación lista y digitalizada antes de iniciar el trámite.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
                    <a href="{{ route('aspirantes.registro') }}"
                       class="px-6 py-3 rounded-lg font-semibold text-white text-sm text-center transition-colors duration-200"
                       style="background-color: #0F4229;"
                       onmouseover="this.style.backgroundColor='#0a2f1c'"
                       onmouseout="this.style.backgroundColor='#0F4229'">
                        Iniciar inscripción
                    </a>
                    <a href="{{ route('aspirantes.seguimiento') }}"
                       class="px-6 py-3 rounded-lg font-semibold text-sm text-center border-2 border-gray-300 text-gray-600 hover:border-gray-400 hover:text-gray-800 transition-colors duration-200">
                        Consultar estatus
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ===== CONTACTO ===== --}}
    <section id="contacto" class="bg-white py-16">
        <div class="container mx-auto px-4 sm:px-8 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-start">

                {{-- Info --}}
                <div class="lg:col-span-2">
                    <p class="text-uicm-gold text-xs font-bold uppercase tracking-widest mb-2">Comunicación</p>
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Contáctanos</h2>
                    <p class="text-gray-500 mb-5 leading-relaxed">
                        Si deseas más información sobre la UICM, procesos de admisión o nuestra
                        plataforma académica, compártenos tus datos y un asesor te contactará.
                    </p>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-uicm-green flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="text-gray-600">{{ $contacto['correo'] }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-uicm-green flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span class="text-gray-600">{{ $contacto['telefono'] }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-uicm-green flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-gray-600">{{ $contacto['horario'] }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Formulario --}}
                <div class="lg:col-span-3">
                    <div class="bg-uicm-gray rounded-2xl shadow-sm p-6 md:p-8">


                        <h5 class="font-bold text-gray-800 text-lg mb-5">Envíanos un mensaje</h5>
                        <form method="POST" action="{{ route('contacto.store') }}">
                            @csrf

                            {{-- Nombre --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}"
                                       maxlength="150"
                                       class="w-full rounded-lg border @error('nombre') border-red-400 bg-red-50 @else border-gray-200 bg-white @enderror px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-uicm-green focus:border-transparent"
                                       placeholder="Nombre y apellidos">
                                @error('nombre')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Correo --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                                <input type="email" name="correo" value="{{ old('correo') }}"
                                       maxlength="150"
                                       class="w-full rounded-lg border @error('correo') border-red-400 bg-red-50 @else border-gray-200 bg-white @enderror px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-uicm-green focus:border-transparent"
                                       placeholder="ejemplo@correo.com">
                                @error('correo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                {{-- Interés --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Interés principal</label>
                                    <select name="interes"
                                            class="w-full rounded-lg border @error('interes') border-red-400 bg-red-50 @else border-gray-200 bg-white @enderror px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-uicm-green focus:border-transparent">
                                        <option value="">Selecciona una opción</option>
                                        @foreach($interesesContacto as $interes)
                                        <option value="{{ $interes->etiqueta }}" {{ old('interes') == $interes->etiqueta ? 'selected' : '' }}>
                                            {{ $interes->etiqueta }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('interes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Teléfono --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                    <input type="tel" name="telefono" value="{{ old('telefono') }}"
                                           maxlength="10"
                                           oninput="formatTelefono(this)"
                                           class="w-full rounded-lg border @error('telefono') border-red-400 bg-red-50 @else border-gray-200 bg-white @enderror px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-uicm-green focus:border-transparent"
                                           placeholder="10 dígitos">
                                    @error('telefono')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Mensaje --}}
                            <div class="mb-5">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                                <textarea name="mensaje" rows="3" maxlength="500"
                                          class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-uicm-green focus:border-transparent resize-none"
                                          placeholder="Cuéntanos brevemente en qué podemos apoyarte.">{{ old('mensaje') }}</textarea>
                            </div>

                            <button type="submit"
                                    class="w-full py-3 rounded-lg text-white font-semibold text-sm transition-colors duration-200"
                                    style="background-color: #0F4229;"
                                    onmouseover="this.style.backgroundColor='#0a2f1c'"
                                    onmouseout="this.style.backgroundColor='#0F4229'">
                                Enviar mensaje
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    // ===== CARRUSEL =====
    const track = document.getElementById('carrusel-track');
    const dots  = document.querySelectorAll('.carrusel-dot');
    const total = 5;
    let current = 0;
    let autoplay;

    function goTo(index) {
        current = (index + total) % total;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => {
            d.classList.toggle('bg-opacity-50', i !== current);
            d.classList.toggle('scale-125', i === current);
        });
    }

    document.getElementById('prev-btn').addEventListener('click', () => { clearInterval(autoplay); goTo(current - 1); startAutoplay(); });
    document.getElementById('next-btn').addEventListener('click', () => { clearInterval(autoplay); goTo(current + 1); startAutoplay(); });
    dots.forEach(d => d.addEventListener('click', () => { clearInterval(autoplay); goTo(parseInt(d.dataset.index)); startAutoplay(); }));

    function startAutoplay() {
        autoplay = setInterval(() => goTo(current + 1), 4000);
    }
    startAutoplay();
</script>
@endpush
