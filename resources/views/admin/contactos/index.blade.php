@extends('layouts.app')

@section('title', 'Mensajes de contacto | UICM')

@section('content')

@php
    $totalContactos  = $contactos->count();
    $totalPendientes = $contactos->where('atendido', false)->count();
    $totalAtendidos  = $contactos->where('atendido', true)->count();
@endphp

<section
    x-data="{
        busqueda: '',
        filtroEstado: 'pendiente',
        filtroInteres: '',
        contadorVisible: {{ $totalPendientes }},
        filtrar() {
            this.$nextTick(() => {
                const cards = this.$refs.lista.querySelectorAll('[data-nombre]');
                let visibles = 0;
                cards.forEach(c => {
                    const texto      = this.busqueda.toLowerCase();
                    const pasaBusq   = !texto
                        || c.dataset.nombre.toLowerCase().includes(texto)
                        || c.dataset.correo.toLowerCase().includes(texto);
                    const pasaEstado = !this.filtroEstado || c.dataset.estado === this.filtroEstado;
                    const pasaInteres = !this.filtroInteres || c.dataset.interes === this.filtroInteres;
                    const mostrar = pasaBusq && pasaEstado && pasaInteres;
                    c.style.display = mostrar ? '' : 'none';
                    if (mostrar) visibles++;
                });
                this.contadorVisible = visibles;
                this.$refs.sinResultados.style.display = (visibles === 0 && {{ $totalContactos }} > 0) ? '' : 'none';
            });
        }
    }"
    x-init="filtrar()"
    class="bg-uicm-gray min-h-screen py-12 px-4">

    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Administración
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Mensajes de contacto</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>




        {{-- Tarjetas de resumen --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            <button type="button"
                    @click="filtroEstado = 'pendiente'; filtrar()"
                    :class="filtroEstado === 'pendiente' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #EFAD5A;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pendientes</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $totalPendientes }}</p>
            </button>

            <button type="button"
                    @click="filtroEstado = 'atendido'; filtrar()"
                    :class="filtroEstado === 'atendido' ? 'ring-2 ring-green-700' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #0F4229;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Atendidos</p>
                <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $totalAtendidos }}</p>
            </button>

            <button type="button"
                    @click="filtroEstado = ''; filtrar()"
                    :class="filtroEstado === '' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #6B7280;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Todos</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $totalContactos }}</p>
            </button>

        </div>

        {{-- Buscador y filtro por interés --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" x-model="busqueda" @input="filtrar()"
                       placeholder="Buscar por nombre o correo…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div class="relative sm:w-56">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <select x-model="filtroInteres" @change="filtrar()"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los intereses</option>
                    @foreach($intereses as $interes)
                        <option value="{{ $interes->etiqueta }}">{{ $interes->etiqueta }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Card contenedor --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Lista de mensajes</h2>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green">
                    <span x-text="contadorVisible"></span> mensaje<span x-show="contadorVisible !== 1">s</span>
                </span>
            </div>

            {{-- Lista de mensajes --}}
            <div class="divide-y divide-gray-400" x-ref="lista">

                @forelse($contactos as $contacto)
                <div data-nombre="{{ strtolower($contacto->nombre) }}"
                     data-correo="{{ strtolower($contacto->correo) }}"
                     data-estado="{{ $contacto->atendido ? 'atendido' : 'pendiente' }}"
                     data-interes="{{ $contacto->interes }}"
                     x-show="filtroEstado === 'pendiente' ? {{ $contacto->atendido ? 'false' : 'true' }} : true"
                     class="px-6 py-5"
                     x-data="{ open: {{ $contacto->atendido ? 'false' : 'true' }} }">

                    <div class="flex flex-col sm:flex-row sm:items-start gap-4">

                        {{-- Avatar + datos --}}
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                 style="{{ $contacto->atendido ? 'background-color: #0F4229;' : 'background-color: #EFAD5A;' }}">
                                {{ strtoupper(substr($contacto->nombre, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-gray-800 text-sm">{{ $contacto->nombre }}</p>
                                    @if($contacto->atendido)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-uicm-green-soft text-uicm-green">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Atendido
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-uicm-orange-soft text-uicm-orange-soft-text">
                                            Pendiente
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 truncate">{{ $contacto->correo }}</p>
                                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: #fef9ec; color: #92670b;">
                                        {{ $contacto->interes }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $contacto->telefono }}</span>
                                    <span class="text-xs text-gray-400">{{ $contacto->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Botón expandir/colapsar --}}
                        <button @click="open = !open"
                                class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors duration-150"
                                :class="open ? 'bg-gray-100 text-gray-600 border-gray-200' : 'border-transparent text-white'"
                                :style="!open ? 'background-color: #0F4229;' : ''">
                            <span x-text="open ? 'Colapsar' : 'Ver mensaje'"></span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Contenido expandible --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="mt-4">

                        @if(!empty($contacto->mensaje))
                        <div class="mb-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Mensaje</p>
                            <div class="bg-gray-50 border-l-4 rounded-r-lg px-4 py-3 text-sm text-gray-700 whitespace-pre-line leading-relaxed max-h-48 overflow-y-auto"
                                 style="border-color: #0F4229;">{{ $contacto->mensaje }}</div>
                        </div>
                        @else
                        <p class="text-xs text-gray-400 italic mb-4">El contacto no dejó mensaje.</p>
                        @endif

                        @if(!$contacto->atendido)
                        <form method="POST" action="{{ route('admin.contactos.responder', $contacto) }}">
                            @csrf
                            <div class="mt-2">
                                <label class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-2">
                                    Responder a {{ $contacto->correo }}
                                </label>
                                <textarea name="respuesta"
                                          rows="4"
                                          placeholder="Escribe tu respuesta aquí..."
                                          class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl resize-none focus:outline-none transition"
                                          onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                                          onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">{{ old('respuesta') }}</textarea>
                                @error('respuesta')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex justify-end mt-3">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white transition-colors duration-150"
                                        style="background-color: #0F4229;"
                                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                                        onmouseout="this.style.backgroundColor='#0F4229'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Enviar respuesta
                                </button>
                            </div>
                        </form>
                        @else
                        <p class="text-xs text-gray-400 mt-1">Este mensaje ya fue atendido.</p>
                        @endif

                    </div>
                </div>
                @empty
                @endforelse

                {{-- Sin resultados del filtro --}}
                <div x-ref="sinResultados" style="display: none;">
                    <div class="px-6 py-14 text-center">
                        <p class="text-sm font-semibold text-gray-500">Sin resultados</p>
                        <p class="text-xs text-gray-400 mt-1">Ningún mensaje coincide con el criterio de búsqueda.</p>
                    </div>
                </div>

                {{-- Lista completamente vacía --}}
                @if($contactos->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #f0f9f4;">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">No hay mensajes de contacto</p>
                    <p class="text-xs text-gray-400 mt-1">Los mensajes del formulario de contacto aparecerán aquí.</p>
                </div>
                @endif

            </div>
        </div>

    </div>
</section>

@endsection
