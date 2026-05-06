@extends('layouts.app')

@section('title', 'Página principal | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4"
         x-data="{ tab: '{{ session('tab_activo', 'oferta') }}' }">
<div class="container mx-auto px-4 lg:px-12 max-w-7xl">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Administración</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Página principal</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>
        <button x-show="tab === 'oferta'" x-cloak
                onclick="document.getElementById('modal-nuevo').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200 self-start sm:self-auto"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2e1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo programa
        </button>
    </div>

    {{-- Mensajes --}}
    @if(session('success_oferta'))
    <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-green-50" style="border-color: #0F4229;">
        <p class="text-sm font-semibold text-green-800">{{ session('success_oferta') }}</p>
    </div>
    @endif
    @if(session('success_carrusel'))
    <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-green-50" style="border-color: #0F4229;">
        <p class="text-sm font-semibold text-green-800">{{ session('success_carrusel') }}</p>
    </div>
    @endif
    @if(session('success_contacto'))
    <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-green-50" style="border-color: #0F4229;">
        <p class="text-sm font-semibold text-green-800">{{ session('success_contacto') }}</p>
    </div>
    @endif
    @if($errors->has('imagen'))
    <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-red-50" style="border-color: #ef4444;">
        <p class="text-sm font-semibold text-red-700">{{ $errors->first('imagen') }}</p>
    </div>
    @elseif($errors->any())
    <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-red-50" style="border-color: #ef4444;">
        @foreach($errors->all() as $error)
            <p class="text-sm text-red-700">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 bg-white rounded-2xl shadow-sm p-1 mb-6 w-fit">
        <button @click="tab = 'oferta'"
                :class="tab === 'oferta' ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                :style="tab === 'oferta' ? 'background-color:#0F4229' : ''"
                class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-150">
            Oferta educativa
        </button>
        <button @click="tab = 'carrusel'"
                :class="tab === 'carrusel' ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                :style="tab === 'carrusel' ? 'background-color:#0F4229' : ''"
                class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-150">
            Carrusel
        </button>
        <button @click="tab = 'contacto'"
                :class="tab === 'contacto' ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                :style="tab === 'contacto' ? 'background-color:#0F4229' : ''"
                class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-150">
            Contacto
        </button>
    </div>

    {{-- ══ TAB: OFERTA EDUCATIVA ══ --}}
    <div x-show="tab === 'oferta'" x-cloak>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Programas registrados</h2>
                <span class="text-xs text-gray-400">{{ $programas->count() }} programa{{ $programas->count() !== 1 ? 's' : '' }}</span>
            </div>

            @if($programas->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #f0f9f4;">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-500">No hay programas registrados</p>
                <p class="text-xs text-gray-400 mt-1">Usa el botón "Nuevo programa" para agregar el primero.</p>
            </div>
            @else
            @php
            $nivelCfg = [
                'licenciatura' => ['label' => 'Licenciaturas', 'badge' => 'Licenciatura', 'color' => '#0F4229'],
                'maestria'     => ['label' => 'Maestrías',     'badge' => 'Maestría',     'color' => '#0891b2'],
                'doctorado'    => ['label' => 'Doctorado',     'badge' => 'Doctorado',    'color' => '#7c3aed'],
            ];
            $agrupados = $programas->groupBy('nivel');
            @endphp
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3">Descripción</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['licenciatura', 'maestria', 'doctorado'] as $nivel)
                        @php $grupo = $agrupados->get($nivel, collect()); $cfg = $nivelCfg[$nivel]; @endphp
                        @if($grupo->count())
                        {{-- Separador de nivel --}}
                        <tr>
                            <td colspan="4" class="px-6 pt-5 pb-2">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-white"
                                          style="background-color: {{ $cfg['color'] }};">
                                        {{ $cfg['label'] }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $grupo->count() }} programa{{ $grupo->count() !== 1 ? 's' : '' }}</span>
                                </div>
                            </td>
                        </tr>
                        @foreach($grupo as $prog)
                        <tr class="hover:bg-gray-50 transition-colors duration-100 border-t border-gray-100">
                            <td class="px-6 py-4 font-semibold text-gray-800 max-w-[200px]">
                                {{ $prog->nombre }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 max-w-[220px] text-xs">
                                {{ $prog->descripcion ? Str::limit($prog->descripcion, 80) : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($prog->activo)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background-color: #0F4229;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Editar --}}
                                    <button type="button"
                                            onclick="abrirEditar({{ $prog->id }}, {{ json_encode($prog->nombre) }}, '{{ $prog->nivel }}', {{ json_encode($prog->descripcion ?? '') }}, {{ json_encode($prog->puntos_clave ?? []) }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                            style="background-color: #D4AF37;"
                                            onmouseover="this.style.backgroundColor='#b8962e'"
                                            onmouseout="this.style.backgroundColor='#D4AF37'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </button>

                                    {{-- Toggle activo --}}
                                    <form method="POST" action="{{ route('admin.pagina-principal.oferta.toggle', $prog) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors duration-150
                                                       {{ $prog->activo ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                            {{ $prog->activo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>

                                    {{-- Eliminar --}}
                                    <form method="POST" action="{{ route('admin.pagina-principal.oferta.destroy', $prog) }}"
                                          onsubmit="return confirm('¿Eliminar el programa «{{ addslashes($prog->nombre) }}»? Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border border-red-200 text-red-500 hover:bg-red-50 transition-colors duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ TAB: CARRUSEL ══ --}}
    @php
        $maxActivas = 8;
        $maxTotal   = 20;
        $totalImagenes  = $imagenes->count();
        $activasActuales = $imagenes->where('activo', true)->count();
    @endphp
    <div x-show="tab === 'carrusel'" x-cloak>

        {{-- Subir imagen --}}
        @if($totalImagenes < $maxTotal)
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6"
             x-data="{
                 archivo: null,
                 preview: null,
                 seleccionar(e) {
                     const file = e.target.files[0];
                     if (!file) return;
                     this.archivo = file.name;
                     const reader = new FileReader();
                     reader.onload = (ev) => { this.preview = ev.target.result; };
                     reader.readAsDataURL(file);
                 },
                 limpiar() {
                     this.archivo = null;
                     this.preview = null;
                     document.getElementById('imagen-upload').value = '';
                 }
             }">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="px-6 py-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-gray-900 mb-0.5">Subir nueva imagen</p>
                        <p class="text-xs text-gray-400">JPG, PNG o WEBP · Máx. 4 MB · Recomendado: 1200×500 px</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                          style="background-color: #f0f9f4; color: #0F4229;">
                        {{ $activasActuales }}/{{ $maxActivas }} activas
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.pagina-principal.carrusel.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Zona de selección --}}
                    <label for="imagen-upload"
                           class="flex flex-col items-center justify-center w-full rounded-xl border-2 border-dashed cursor-pointer transition-colors duration-200 mb-4"
                           :class="archivo ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-gray-400'"
                           style="min-height: 140px;">

                        {{-- Sin imagen seleccionada --}}
                        <template x-if="!preview">
                            <div class="flex flex-col items-center py-6 px-4 text-center">
                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm font-semibold text-gray-500">Haz clic para seleccionar una imagen</p>
                                <p class="text-xs text-gray-400 mt-1">Solo se sube una imagen a la vez</p>
                            </div>
                        </template>

                        {{-- Con imagen seleccionada: preview --}}
                        <template x-if="preview">
                            <div class="w-full p-3">
                                <img :src="preview" alt="Vista previa"
                                     class="w-full rounded-lg object-cover shadow-sm"
                                     style="max-height: 200px; object-position: center;">
                                <div class="flex items-center gap-2 mt-2 px-1">
                                    <svg class="w-4 h-4 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-xs font-semibold text-green-700 truncate" x-text="archivo"></span>
                                </div>
                            </div>
                        </template>
                    </label>

                    <input type="file" id="imagen-upload" name="imagen"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="hidden"
                           @change="seleccionar($event)">

                    @error('imagen')
                    <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" x-show="archivo" x-cloak
                                @click="limpiar()"
                                class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                :disabled="!archivo"
                                class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
                                style="background-color:#0F4229;"
                                onmouseover="if(!this.disabled) this.style.backgroundColor='#0a2e1c'"
                                onmouseout="this.style.backgroundColor='#0F4229'">
                            Subir imagen
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="mb-6 rounded-xl px-5 py-4 border-l-4 bg-yellow-50" style="border-color: #D4AF37;">
            <p class="text-sm font-semibold text-yellow-800">Límite de almacenamiento alcanzado ({{ $maxTotal }}/{{ $maxTotal }})</p>
            <p class="text-xs text-yellow-700 mt-0.5">Para agregar una nueva imagen, primero elimina alguna de las actuales.</p>
        </div>
        @endif

        {{-- Grid imágenes --}}
        @if($totalImagenes > 0)
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Imágenes del carrusel</h2>
                <span class="text-xs text-gray-400">{{ $activasActuales }}/{{ $maxActivas }} activas · {{ $totalImagenes }} guardadas</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($imagenes as $img)
                    <div class="rounded-xl overflow-hidden shadow-sm border-2 transition-all duration-150
                                {{ $img->activo ? 'border-green-400' : 'border-gray-200' }}">

                        {{-- Thumbnail con badge de estado --}}
                        <div class="relative {{ !$img->activo ? 'opacity-50' : '' }}">
                            <img src="{{ $img->url }}" alt="Imagen carrusel"
                                 class="w-full object-cover" style="height: 110px; object-position: center;">
                            <div class="absolute top-2 left-2">
                                <span class="text-xs bg-black bg-opacity-60 text-white px-2 py-0.5 rounded-full font-semibold">#{{ $img->orden }}</span>
                            </div>
                            <div class="absolute top-2 right-2">
                                @if($img->activo)
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-green-500 text-white shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80"></span>
                                    Activa
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-gray-600 text-gray-200 shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Oculta
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- Botones siempre visibles --}}
                        <div class="flex items-center justify-between gap-1 px-2 py-2 bg-white">
                            <form method="POST" action="{{ route('admin.pagina-principal.carrusel.toggle', $img) }}">
                                @csrf @method('PATCH')
                                @if(!$img->activo && $activasActuales >= $maxActivas)
                                <button type="button" disabled
                                        title="Ya hay {{ $maxActivas }} activas. Oculta alguna primero."
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-300 cursor-not-allowed">
                                    Mostrar
                                </button>
                                @else
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors duration-150
                                               {{ $img->activo ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                    {{ $img->activo ? 'Ocultar' : 'Mostrar' }}
                                </button>
                                @endif
                            </form>
                            <form method="POST" action="{{ route('admin.pagina-principal.carrusel.destroy', $img) }}"
                                  onsubmit="return confirm('¿Eliminar esta imagen del carrusel?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-red-200 text-red-500 hover:bg-red-50 transition-colors duration-150">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-semibold text-gray-500">No hay imágenes en el carrusel</p>
                <p class="text-xs text-gray-400 mt-1">Sube la primera imagen con el formulario de arriba.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- ══ TAB: CONTACTO ══ --}}
    <div x-show="tab === 'contacto'" x-cloak>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Información de contacto</h2>
                <span class="text-xs text-gray-400">Datos visibles en el sitio público</span>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                    {{-- Panel izquierdo: descripción --}}
                    <div class="lg:col-span-2">
                        <p class="text-sm font-semibold text-gray-800 mb-1">¿Qué datos se muestran aquí?</p>
                        <p class="text-sm text-gray-500 leading-relaxed mb-5">
                            Estos tres campos aparecen en la sección <strong class="text-gray-700">Contáctanos</strong>
                            de la página pública. Cualquier cambio que guardes se refleja de inmediato en el sitio.
                        </p>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color:#0F4229;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-700">Correo electrónico</p>
                                    <p class="text-xs text-gray-400">{{ $contacto['correo'] }}</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color:#0F4229;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-700">Teléfono</p>
                                    <p class="text-xs text-gray-400">{{ $contacto['telefono'] }}</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color:#0F4229;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-700">Horario de atención</p>
                                    <p class="text-xs text-gray-400">{{ $contacto['horario'] }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    {{-- Panel derecho: formulario --}}
                    <div class="lg:col-span-3">
                        <form method="POST" action="{{ route('admin.pagina-principal.contacto') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                                <input type="email" name="correo" value="{{ old('correo', $contacto['correo']) }}" required
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm text-gray-900 focus:outline-none"
                                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                                @error('correo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input type="text" name="telefono" value="{{ old('telefono', $contacto['telefono']) }}" required
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm text-gray-900 focus:outline-none"
                                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                                @error('telefono')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Horario de atención</label>
                                <input type="text" name="horario" value="{{ old('horario', $contacto['horario']) }}" required
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm text-gray-900 focus:outline-none"
                                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                                @error('horario')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="pt-2 flex justify-end">
                                <button type="submit"
                                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-200"
                                        style="background-color:#0F4229;"
                                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                                        onmouseout="this.style.backgroundColor='#0F4229'">
                                    Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
</section>

{{-- ═══ MODAL: NUEVO PROGRAMA ═══ --}}
<div id="modal-nuevo" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto"
     style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto">
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Nuevo programa</h3>
            <button onclick="document.getElementById('modal-nuevo').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.pagina-principal.oferta.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del programa <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" required
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                       placeholder="ej: Licenciatura en Administración de Empresas">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nivel <span class="text-red-500">*</span></label>
                <select name="nivel" required
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none appearance-none bg-white"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Seleccionar…</option>
                    <option value="licenciatura">Licenciatura</option>
                    <option value="maestria">Maestría</option>
                    <option value="doctorado">Doctorado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none resize-none"
                          onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                          onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                          placeholder="Descripción general del programa…"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Puntos clave</label>
                <p class="text-xs text-gray-400 mb-1.5">Un punto por línea.</p>
                <textarea name="puntos_clave" rows="4"
                          class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none resize-none font-mono"
                          onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                          onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                          placeholder="Línea de formación 1&#10;Línea de formación 2&#10;Línea de formación 3"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-nuevo').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    Guardar programa
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ MODAL: EDITAR PROGRAMA ═══ --}}
<div id="modal-editar" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto"
     style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto">
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #D4AF37;"></div>
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Editar programa</h3>
            <button onclick="document.getElementById('modal-editar').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-editar" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del programa <span class="text-red-500">*</span></label>
                <input type="text" id="edit-nombre" name="nombre" required
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nivel <span class="text-red-500">*</span></label>
                <select id="edit-nivel" name="nivel" required
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none appearance-none bg-white"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="licenciatura">Licenciatura</option>
                    <option value="maestria">Maestría</option>
                    <option value="doctorado">Doctorado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="edit-descripcion" name="descripcion" rows="3"
                          class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none resize-none"
                          onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                          onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Puntos clave</label>
                <p class="text-xs text-gray-400 mb-1.5">Un punto por línea.</p>
                <textarea id="edit-puntos" name="puntos_clave" rows="4"
                          class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none resize-none font-mono"
                          onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                          onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-editar').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
                        style="background-color: #D4AF37;"
                        onmouseover="this.style.backgroundColor='#b8962e'"
                        onmouseout="this.style.backgroundColor='#D4AF37'">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function abrirEditar(id, nombre, nivel, descripcion, puntosClave) {
    document.getElementById('edit-nombre').value = nombre;
    document.getElementById('edit-nivel').value = nivel;
    document.getElementById('edit-descripcion').value = descripcion || '';
    document.getElementById('edit-puntos').value = Array.isArray(puntosClave) ? puntosClave.join('\n') : '';
    document.getElementById('form-editar').action = '/admin/pagina-principal/oferta/' + id;
    document.getElementById('modal-editar').classList.remove('hidden');
}

document.getElementById('modal-nuevo').addEventListener('click', function (e) {
    if (e.target === this) this.classList.add('hidden');
});
document.getElementById('modal-editar').addEventListener('click', function (e) {
    if (e.target === this) this.classList.add('hidden');
});

@if($errors->any())
document.getElementById('modal-nuevo').classList.remove('hidden');
@endif
</script>
@endpush

@endsection
