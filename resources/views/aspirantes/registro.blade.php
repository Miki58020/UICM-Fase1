@extends('layouts.app')

@section('title', 'Registro de Aspirante | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12">
    <div class="container mx-auto px-8 lg:px-24">

        {{-- Encabezado de página --}}
        <div class="text-center mb-8">
            <p class="text-uicm-gold text-xs font-bold uppercase tracking-widest mb-2">Admisiones</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">Registro de Aspirante</h1>
            {{-- Línea decorativa oro --}}
            <div class="mx-auto w-20 h-1 rounded-full" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Card principal --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden max-w-4xl mx-auto">

            {{-- Banner superior verde --}}
            <div class="h-2 w-full" style="background-color: #0F4229;"></div>

            <div class="p-6 md:p-10">
                <form method="POST"
                      action="{{ route('aspirantes.confirmacion') }}"
                      enctype="multipart/form-data">
                    @csrf

                    {{-- ══════════════════════════════════════════
                         SECCIÓN 1: DATOS PERSONALES
                    ══════════════════════════════════════════ --}}
                    <div class="mb-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                 style="background-color: #0F4229;">1</div>
                            <h2 class="text-lg font-bold text-gray-800">Datos personales</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Nombre completo --}}
                            <div class="md:col-span-2">
                                <label for="nombre_completo"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre completo <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="nombre_completo"
                                       name="nombre_completo"
                                       placeholder="Apellido Apellido, Nombre(s)"
                                       required
                                       class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>

                            {{-- CURP --}}
                            <div>
                                <label for="curp"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    CURP <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="curp"
                                       name="curp"
                                       placeholder="18 caracteres"
                                       maxlength="18"
                                       required
                                       class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm uppercase focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>

                            {{-- Fecha de nacimiento --}}
                            <div>
                                <label for="fecha_nacimiento"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha de nacimiento <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       id="fecha_nacimiento"
                                       name="fecha_nacimiento"
                                       required
                                       class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>

                            {{-- Teléfono --}}
                            <div>
                                <label for="telefono"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    Teléfono <span class="text-red-500">*</span>
                                </label>
                                <input type="tel"
                                       id="telefono"
                                       name="telefono"
                                       placeholder="10 dígitos"
                                       required
                                       maxlength="10"
                                       oninput="this.value=this.value.replace(/\D/g,'')"
                                       class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>

                            {{-- Correo electrónico --}}
                            <div>
                                <label for="email"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    Correo electrónico <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       placeholder="ejemplo@correo.com"
                                       required
                                       class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                                       style="--tw-ring-color: #0F4229;">
                            </div>

                        </div>
                    </div>

                    {{-- Divisor --}}
                    <hr class="border-gray-100 mb-10">

                    {{-- ══════════════════════════════════════════
                         SECCIÓN 2: PROGRAMA ACADÉMICO
                    ══════════════════════════════════════════ --}}
                    <div class="mb-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                 style="background-color: #0F4229;">2</div>
                            <h2 class="text-lg font-bold text-gray-800">Programa académico</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Programa --}}
                            <div>
                                <label for="programa_academico"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    Programa de interés <span class="text-red-500">*</span>
                                </label>
                                <select id="programa_academico"
                                        name="programa_academico"
                                        required
                                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:border-transparent"
                                        style="--tw-ring-color: #0F4229;">
                                    <option value="" disabled selected>Selecciona un programa</option>
                                    <optgroup label="Licenciaturas">
                                        <option value="psicopedagogia">Psicopedagogía</option>
                                        <option value="lengua_inglesa">Lengua Inglesa</option>
                                        <option value="administracion">Administración</option>
                                        <option value="lengua_literatura">Lengua y Literatura</option>
                                    </optgroup>
                                    <optgroup label="Maestrías">
                                        <option value="mba">Administración y Negocios</option>
                                        <option value="maestria_educacion">Educación</option>
                                        <option value="negocios_logistica">Negocios y Logística Internacional</option>
                                    </optgroup>
                                    <optgroup label="Doctorado">
                                        <option value="doctorado_educacion">Doctorado en Educación</option>
                                    </optgroup>
                                </select>
                            </div>

                            {{-- Generación --}}
                            <div>
                                <label for="generacion"
                                       class="block text-sm font-medium text-gray-700 mb-1">
                                    Generación <span class="text-red-500">*</span>
                                </label>
                                <select id="generacion"
                                        name="generacion"
                                        required
                                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:border-transparent"
                                        style="--tw-ring-color: #0F4229;">
                                    <option value="" disabled selected>Selecciona una generación</option>
                                    <option value="2026-1">2026-1 (Enero – Junio)</option>
                                    <option value="2026-2">2026-2 (Agosto – Diciemb­re)</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    {{-- Divisor --}}
                    <hr class="border-gray-100 mb-10">

                    {{-- ══════════════════════════════════════════
                         SECCIÓN 3: DOCUMENTACIÓN
                    ══════════════════════════════════════════ --}}
                    <div class="mb-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                 style="background-color: #0F4229;">3</div>
                            <h2 class="text-lg font-bold text-gray-800">Documentación</h2>
                        </div>

                        <p class="text-gray-500 text-sm mb-5 leading-relaxed">
                            Adjunta los documentos requeridos en formato <strong>PDF, JPG o PNG</strong>.
                            Tamaño máximo por archivo: 5 MB.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                            {{-- Acta de nacimiento --}}
                            <div x-data="{ archivo: null }">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Acta de nacimiento <span class="text-red-500">*</span>
                                </label>
                                <label for="acta_nacimiento"
                                       class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed rounded-lg cursor-pointer transition-colors duration-200"
                                       :class="archivo ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'">
                                    <template x-if="!archivo">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-7 h-7 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span class="text-xs text-gray-500">Haz clic para subir</span>
                                        </div>
                                    </template>
                                    <template x-if="archivo">
                                        <div class="flex flex-col items-center px-2">
                                            <svg class="w-6 h-6 text-green-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-xs font-semibold text-green-700 text-center break-all" x-text="archivo"></span>
                                        </div>
                                    </template>
                                </label>
                                <input id="acta_nacimiento" name="acta_nacimiento"
                                       type="file" accept=".pdf,.jpg,.jpeg,.png"
                                       required class="sr-only"
                                       @change="archivo = $event.target.files[0]?.name ?? null">
                            </div>

                            {{-- Certificado --}}
                            <div x-data="{ archivo: null }">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Certificado de estudios <span class="text-red-500">*</span>
                                </label>
                                <label for="certificado"
                                       class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed rounded-lg cursor-pointer transition-colors duration-200"
                                       :class="archivo ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'">
                                    <template x-if="!archivo">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-7 h-7 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span class="text-xs text-gray-500">Haz clic para subir</span>
                                        </div>
                                    </template>
                                    <template x-if="archivo">
                                        <div class="flex flex-col items-center px-2">
                                            <svg class="w-6 h-6 text-green-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-xs font-semibold text-green-700 text-center break-all" x-text="archivo"></span>
                                        </div>
                                    </template>
                                </label>
                                <input id="certificado" name="certificado"
                                       type="file" accept=".pdf,.jpg,.jpeg,.png"
                                       required class="sr-only"
                                       @change="archivo = $event.target.files[0]?.name ?? null">
                            </div>

                            {{-- Identificación --}}
                            <div x-data="{ archivo: null }">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Identificación oficial <span class="text-red-500">*</span>
                                </label>
                                <label for="identificacion"
                                       class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed rounded-lg cursor-pointer transition-colors duration-200"
                                       :class="archivo ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'">
                                    <template x-if="!archivo">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-7 h-7 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span class="text-xs text-gray-500">Haz clic para subir</span>
                                        </div>
                                    </template>
                                    <template x-if="archivo">
                                        <div class="flex flex-col items-center px-2">
                                            <svg class="w-6 h-6 text-green-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-xs font-semibold text-green-700 text-center break-all" x-text="archivo"></span>
                                        </div>
                                    </template>
                                </label>
                                <input id="identificacion" name="identificacion"
                                       type="file" accept=".pdf,.jpg,.jpeg,.png"
                                       required class="sr-only"
                                       @change="archivo = $event.target.files[0]?.name ?? null">
                            </div>

                        </div>
                    </div>

                    {{-- Nota informativa --}}
                    <div class="rounded-lg p-4 mb-8 border-l-4 bg-orange-50"
                         style="border-color: #EFAD5A;">
                        <p class="text-sm text-gray-600 leading-relaxed">
                            <strong class="text-gray-800">Nota:</strong>
                            Al enviar este formulario, tu solicitud quedará en estado
                            <span class="font-semibold text-uicm-green">pendiente de validación</span>.
                            Recibirás un correo electrónico con el folio asignado y los pasos a seguir.
                        </p>
                    </div>

                    {{-- Botón enviar --}}
                    <button type="submit"
                            class="w-full py-3.5 rounded-xl text-white font-bold text-base tracking-wide transition-colors duration-200 shadow-md"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2f1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        Enviar solicitud
                    </button>

                </form>
            </div>
        </div>

    </div>
</section>

@endsection
