@extends('layouts.app')

@php
    // La vista la comparten las tres áreas; cada una atiende a un público distinto
    // (ver SolicitudContrasenaController::listar). El título lo dice explícitamente
    // porque las tres entradas del menú se llaman igual.
    $area = match ($tipo) {
        'profesores' => 'Coordinación Académica',
        'alumnos'    => 'Control Escolar',
        default      => 'Administración',
    };
    $publico = match ($tipo) {
        'profesores' => 'profesores',
        'alumnos'    => 'alumnos',
        default      => 'personal administrativo',
    };
    $titulo = 'Recuperación de contraseñas de ' . $publico;
@endphp

@section('title', $titulo . ' | UICM')

@section('content')

<section class="bg-uicm-gray min-h-screen py-12 px-4">

    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                {{ $area }}
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">
                {{ $titulo }}
            </h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        {{-- Tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Solicitudes pendientes</h2>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green">
                    {{ $solicitudes->count() }} pendiente{{ $solicitudes->count() !== 1 ? 's' : '' }}
                </span>
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
                            'profesor'        => 'Profesor',
                        ];
                        $rolLabel = $rolLabels[$solicitud->user->rol] ?? $solicitud->user->rol;
                        $rolBadgeColors = [
                            'admin'           => ['#d7ede1', '#0F4229'],
                            'control_escolar' => ['#fbdba8', '#b0530a'],
                            'finanzas'        => ['#f7e7ab', '#8a6d0a'],
                            'coordinacion'    => ['#cfe0f7', '#164a99'],
                        ];
                        [$rolBadgeBg, $rolBadgeText] = $rolBadgeColors[$solicitud->user->rol] ?? ['#fef9ec', '#92670b'];
                        $routeAtender = $tipo === 'profesores'
                            ? 'admin.contrasenas-profesores.atender'
                            : 'admin.solicitudes-contrasena.atender';
                    @endphp

                    <div class="px-6 py-5">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">

                            {{-- Avatar + datos del usuario --}}
                            <div class="flex items-start gap-3 flex-1">
                                <div class="w-10 h-10 rounded-full flex-shrink-0 overflow-hidden border-2"
                                     style="border-color: #0F4229;">
                                    @if($solicitud->user->foto)
                                        <img src="{{ route('admin.archivo', ['path' => $solicitud->user->foto]) }}"
                                             alt="Foto de perfil"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-white text-sm font-bold"
                                             style="background-color: #0F4229;">
                                            {{ strtoupper(substr($solicitud->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-gray-800 text-sm">{{ $solicitud->user->nombre_completo }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: {{ $rolBadgeBg }}; color: {{ $rolBadgeText }};">
                                            {{ $rolLabel }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-400">{{ $solicitud->user->email }}</p>
                                    <p class="text-xs text-gray-400 mt-1.5">
                                        Solicitado el {{ $solicitud->created_at->format('d/m/Y \a \l\a\s H:i') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Formulario para atender --}}
                            <form method="POST"
                                  action="{{ route($routeAtender, $solicitud) }}"
                                  class="flex items-center gap-2 w-full sm:w-auto"
                                  onsubmit="return confirm('¿Generar una contraseña nueva y enviarla a {{ addslashes($solicitud->user->email) }}?')">
                                @csrf

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
