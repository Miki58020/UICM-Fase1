@extends('layouts.app')

@section('title', 'Oferta educativa | UICM')

@section('content')

{{-- Hero --}}
<section class="bg-uicm-green text-white py-16">
    <div class="container mx-auto px-4 sm:px-8 lg:px-12">
        <p class="text-green-300 text-xs font-bold uppercase tracking-widest mb-2">Programas académicos</p>
        <h1 class="text-4xl font-extrabold mb-3">Oferta educativa</h1>
        <p class="text-green-100 max-w-2xl leading-relaxed">
            La Universidad Internacional Cuba México ofrece programas de licenciatura, maestría y doctorado orientados
            a la formación integral y al desarrollo profesional en distintos ámbitos del conocimiento.
        </p>

        {{-- Tabs de nivel --}}
        <div class="flex flex-wrap gap-2 mt-8">
            @foreach(['licenciatura' => 'Licenciaturas', 'maestria' => 'Maestrías', 'doctorado' => 'Doctorado'] as $key => $label)
            @if(isset($programas[$key]) && $programas[$key]->count())
            <a href="#{{ $key }}"
               class="px-5 py-2 rounded-lg text-sm font-semibold border-2 border-white transition-colors duration-150"
               style="background: rgba(255,255,255,0.15);"
               onmouseover="this.style.background='rgba(255,255,255,0.28)'"
               onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                {{ $label }}
            </a>
            @endif
            @endforeach
        </div>
    </div>
</section>

{{-- Programas por nivel --}}
@php
$subtitulos = [
    'licenciatura' => 'Estudios profesionales orientados a la formación técnica, humanística, tecnológica y práctica académica.',
    'maestria'     => 'Estudios de posgrado orientados al fortalecimiento de competencias profesionales y al desarrollo de proyectos especializados.',
    'doctorado'    => 'Formación avanzada para profesionales interesados en la investigación y la innovación educativa.',
];
$labels = [
    'licenciatura' => 'Licenciaturas',
    'maestria'     => 'Maestrías',
    'doctorado'    => 'Doctorado',
];
$titulos = [
    'licenciatura' => 'Programas de Licenciatura',
    'maestria'     => 'Programas de Maestría',
    'doctorado'    => 'Doctorado',
];
@endphp

@foreach(['licenciatura', 'maestria', 'doctorado'] as $nivel)
@if(isset($programas[$nivel]) && $programas[$nivel]->count())

<section id="{{ $nivel }}" class="{{ $loop->even ? 'bg-uicm-gray' : 'bg-white' }} py-16">
    <div class="container mx-auto px-4 sm:px-8 lg:px-12">

        <div class="mb-10">
            <p class="text-uicm-gold text-xs font-bold uppercase tracking-widest mb-2">{{ $labels[$nivel] }}</p>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $titulos[$nivel] }}</h2>
            <p class="text-gray-500">{{ $subtitulos[$nivel] }}</p>
        </div>

        @if($nivel === 'doctorado')
            {{-- Doctorado: layout diferente (más amplio) --}}
            @foreach($programas[$nivel] as $prog)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                <div>
                    <h3 class="text-2xl font-bold text-uicm-green mb-3">{{ $prog->nombre }}</h3>
                    @if($prog->descripcion)
                    <p class="text-gray-600 leading-relaxed mb-4">{{ $prog->descripcion }}</p>
                    @endif
                </div>
                @if($prog->puntos_clave && count($prog->puntos_clave))
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h4 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Líneas generales de formación</h4>
                    <ul class="space-y-2">
                        @foreach($prog->puntos_clave as $punto)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-uicm-gold flex-shrink-0 mt-1.5"></span>
                            {{ $punto }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @endforeach
        @else
            {{-- Licenciaturas y Maestrías: grid de tarjetas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($programas[$nivel] as $prog)
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <h3 class="font-bold text-uicm-green text-lg mb-2">{{ $prog->nombre }}</h3>
                    @if($prog->descripcion)
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">{{ $prog->descripcion }}</p>
                    @endif
                    @if($prog->puntos_clave && count($prog->puntos_clave))
                    <ul class="space-y-1.5">
                        @foreach($prog->puntos_clave as $punto)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-uicm-gold flex-shrink-0 mt-1.5"></span>
                            {{ $punto }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endforeach
            </div>
        @endif

    </div>
</section>
@endif
@endforeach


{{-- Info de contacto --}}
<section id="contacto" class="bg-uicm-gray py-16">
    <div class="container mx-auto px-4 sm:px-8 lg:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-start">
            <div class="lg:col-span-2">
                <p class="text-uicm-gold text-xs font-bold uppercase tracking-widest mb-2">Comunicación</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-4">Contáctanos</h2>
                <p class="text-gray-500 mb-5 leading-relaxed text-sm">
                    Si deseas más información sobre la oferta educativa de la UICM, compártenos tus datos y un asesor te contactará.
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
            <div class="lg:col-span-3">
                <div class="bg-uicm-gray rounded-2xl shadow-sm p-6 md:p-8">
                    <h5 class="font-bold text-gray-800 text-lg mb-5">Envíanos un mensaje</h5>
                    <form method="POST" action="{{ route('contacto.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}"
                                   maxlength="150"
                                   class="w-full rounded-lg border @error('nombre') border-red-400 bg-red-50 @else border-gray-200 bg-white @enderror px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-uicm-green focus:border-transparent"
                                   placeholder="Nombre y apellidos">
                            @error('nombre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                            <input type="email" name="correo" value="{{ old('correo') }}"
                                   maxlength="150"
                                   class="w-full rounded-lg border @error('correo') border-red-400 bg-red-50 @else border-gray-200 bg-white @enderror px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-uicm-green focus:border-transparent"
                                   placeholder="ejemplo@correo.com">
                            @error('correo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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
                                @error('interes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input type="tel" name="telefono" value="{{ old('telefono') }}"
                                       maxlength="10"
                                       oninput="formatTelefono(this)"
                                       class="w-full rounded-lg border @error('telefono') border-red-400 bg-red-50 @else border-gray-200 bg-white @enderror px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-uicm-green focus:border-transparent"
                                       placeholder="10 dígitos">
                                @error('telefono')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
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
