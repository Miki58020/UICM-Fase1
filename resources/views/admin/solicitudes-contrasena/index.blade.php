@extends('layouts.app')

@section('title', 'Solicitudes de contraseña | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">

    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Administración
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Solicitudes de contraseña</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Solicitudes pendientes</h2>
                <span class="text-xs text-gray-400">{{ $solicitudes->count() }} pendiente{{ $solicitudes->count() !== 1 ? 's' : '' }}</span>
            </div>

            @if($solicitudes->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #f0f9f4;">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                     a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964
                                     A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">No hay solicitudes pendientes</p>
                    <p class="text-xs text-gray-400 mt-1">Todas las solicitudes han sido atendidas.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($solicitudes as $solicitud)
                    @php
                        $rolLabels = [
                            'admin'           => 'Administrador',
                            'control_escolar' => 'Control Escolar',
                            'finanzas'        => 'Finanzas',
                            'coordinacion'    => 'Coordinación Académica',
                            'alumno'          => 'Alumno',
                        ];
                        $rolLabel = $rolLabels[$solicitud->user->rol] ?? $solicitud->user->rol;
                    @endphp

                    <div class="px-6 py-5">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">

                            {{-- Avatar + datos del usuario --}}
                            <div class="flex items-start gap-3 flex-1">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                     style="background-color: #0F4229;">
                                    {{ strtoupper(substr($solicitud->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">{{ $solicitud->user->nombre_completo }}</p>
                                    <p class="text-xs text-gray-400">{{ $solicitud->user->email }}</p>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: #fef9ec; color: #92670b;">
                                            {{ $rolLabel }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            Solicitado el {{ $solicitud->created_at->format('d/m/Y \a \l\a\s H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Formulario para atender --}}
                            <form method="POST"
                                  action="{{ route('admin.solicitudes-contrasena.atender', $solicitud) }}"
                                  class="flex items-center gap-2 flex-shrink-0">
                                @csrf

                                <div>
                                    <input type="text"
                                           name="password"
                                           placeholder="Nueva contraseña"
                                           autocomplete="off"
                                           class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none w-44"
                                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                                    @error('password')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors duration-150"
                                        style="background-color: #0F4229;"
                                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                                        onmouseout="this.style.backgroundColor='#0F4229'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                                 a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964
                                                 A6 6 0 1121 9z"/>
                                    </svg>
                                    Atender
                                </button>
                            </form>

                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</section>

@endsection
