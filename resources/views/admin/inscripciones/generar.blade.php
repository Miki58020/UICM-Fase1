@extends('layouts.app')

@section('title', 'Alumno Inscrito | UICM')

@section('content')

@php
// Datos simulados del alumno recién inscrito
$alumno = [
    'nombre'    => 'Juan Pérez García',
    'programa'  => 'Ingeniería en Sistemas Computacionales',
    'matricula' => 'UICM-2026-IS-001',
    'grupo'     => 'IS-101',
    'folio'     => 'UICM-2026-0001',
    'email'     => 'juan.perez@uicm.edu.mx',
    'password'  => 'Abc12345',
];
@endphp

<section class="bg-uicm-gray min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg">

        {{-- Card principal --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>

            <div class="px-8 py-8">

                {{-- Ícono de éxito --}}
                <div class="flex items-center justify-center w-16 h-16 rounded-full mx-auto mb-5"
                     style="background-color: #f0f9f4;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                {{-- Título --}}
                <div class="text-center mb-6">
                    <h1 class="text-xl font-extrabold text-gray-900 mb-2">
                        Alumno inscrito correctamente
                    </h1>
                    <div class="w-12 h-0.5 mx-auto" style="background-color: #D4AF37;"></div>
                </div>

                {{-- Datos de la inscripción --}}
                <div class="bg-uicm-gray rounded-xl p-5 mb-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-sm">

                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Nombre completo</dt>
                            <dd class="font-bold text-gray-900">{{ $alumno['nombre'] }}</dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Programa</dt>
                            <dd class="font-semibold text-gray-800">{{ $alumno['programa'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Matrícula</dt>
                            <dd class="font-bold font-mono tracking-widest text-sm" style="color: #0F4229;">
                                {{ $alumno['matricula'] }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Grupo asignado</dt>
                            <dd class="font-bold text-gray-900">{{ $alumno['grupo'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Folio de admisión</dt>
                            <dd class="font-mono text-xs text-gray-600">{{ $alumno['folio'] }}</dd>
                        </div>

                    </dl>
                </div>

                {{-- Caja de credenciales — borde dorado --}}
                <div class="rounded-xl p-5 border-l-4 mb-6"
                     style="border-color: #D4AF37; background-color: #fffdf0;">

                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #D4AF37;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                     a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <p class="text-xs font-bold uppercase tracking-widest" style="color: #D4AF37;">
                            Credenciales generadas
                        </p>
                    </div>

                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Usuario</dt>
                            <dd class="font-bold font-mono text-gray-800 break-all">{{ $alumno['email'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-0.5">Contraseña temporal</dt>
                            <dd class="font-bold font-mono text-gray-800 tracking-widest">{{ $alumno['password'] }}</dd>
                        </div>
                    </dl>

                    <p class="text-xs text-gray-500 mt-4 leading-relaxed">
                        Estas credenciales serán enviadas al correo del alumno.
                        Se solicitará cambio de contraseña en el primer inicio de sesión.
                    </p>
                </div>

                {{-- Botones --}}
                <div class="flex flex-col gap-3">

                    {{-- Enviar credenciales (simulado) --}}
                    <button type="button"
                            class="w-full inline-flex items-center justify-center gap-2
                                   py-3 rounded-xl text-sm font-bold text-white
                                   transition-colors duration-200 shadow-sm"
                            style="background-color: #EFAD5A;"
                            onmouseover="this.style.backgroundColor='#e09a3a'"
                            onmouseout="this.style.backgroundColor='#EFAD5A'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7
                                     a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Enviar credenciales
                    </button>

                    {{-- Volver a la lista --}}
                    <a href="{{ route('admin.inscripciones.index') }}"
                       class="w-full inline-flex items-center justify-center gap-2
                              py-3 rounded-xl text-sm font-bold border-2
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
        </div>

    </div>
</section>

@endsection
