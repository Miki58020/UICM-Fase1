@extends('layouts.app')

@section('title', 'Aclaraciones de calificación | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Coordinación Académica
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Aclaraciones de calificación</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        <div class="space-y-6">

            {{-- Filtro de estado --}}
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>
                <div class="px-6 py-4 flex flex-wrap gap-2">
                    @foreach (['pendiente' => 'Pendientes', 'aprobada' => 'Aprobadas', 'rechazada' => 'Rechazadas', 'todas' => 'Todas'] as $valor => $label)
                        <a href="{{ route('admin.aclaraciones.index', ['estado' => $valor]) }}"
                           class="px-4 py-2 rounded-xl text-xs font-bold transition-colors
                                  {{ $estado === $valor ? 'text-white' : 'text-gray-600 bg-gray-100' }}"
                           style="{{ $estado === $valor ? 'background-color: #0F4229;' : '' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($aclaraciones->isEmpty())
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
                <div class="px-6 py-12 flex flex-col items-center text-center">
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin aclaraciones</h3>
                    <p class="text-sm text-gray-500 max-w-xs">No hay solicitudes de aclaración con este filtro.</p>
                </div>
            </div>
            @else

            @foreach ($aclaraciones as $aclaracion)
            <div class="bg-white rounded-2xl shadow-md overflow-hidden" x-data="{ rechazando: false }">
                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

                <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm">
                            {{ $aclaracion->alumno->nombre_completo }}
                            <span class="text-xs text-gray-400 font-mono">({{ $aclaracion->alumno->matricula }})</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $aclaracion->cargaAcademica->materia->nombre ?? '—' }}
                            &nbsp;·&nbsp; Grupo {{ $aclaracion->cargaAcademica->grupo->clave ?? '—' }}
                            &nbsp;·&nbsp; Tipo: <strong>{{ ucfirst($aclaracion->tipo) }}</strong>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Solicitado por: {{ $aclaracion->profesor->nombre ?? '—' }}
                            el {{ $aclaracion->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                          {{ $aclaracion->estado === 'pendiente' ? 'bg-amber-100 text-amber-700' : ($aclaracion->estado === 'aprobada' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                        {{ ucfirst($aclaracion->estado) }}
                    </span>
                </div>

                <div class="px-6 py-4">
                    <p class="text-sm text-gray-700">
                        Calificación propuesta:
                        <strong class="{{ $aclaracion->calificacion_propuesta >= 7.0 ? 'text-green-700' : 'text-red-600' }}">
                            {{ number_format($aclaracion->calificacion_propuesta, 1) }}
                        </strong>
                    </p>
                    <p class="text-sm text-gray-500 mt-2"><strong>Motivo:</strong> {{ $aclaracion->motivo }}</p>

                    @if ($aclaracion->estado === 'rechazada' && $aclaracion->motivo_rechazo)
                        <p class="text-sm text-red-600 mt-2"><strong>Motivo de rechazo:</strong> {{ $aclaracion->motivo_rechazo }}</p>
                    @endif
                </div>

                @if ($aclaracion->estado === 'pendiente')
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-2" x-show="!rechazando">
                    <form method="POST" action="{{ route('admin.aclaraciones.aprobar', $aclaracion) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white"
                                style="background-color: #0F4229;">
                            Aprobar
                        </button>
                    </form>
                    <button type="button" @click="rechazando = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-red-100 text-red-700">
                        Rechazar
                    </button>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50" x-show="rechazando" x-cloak>
                    <form method="POST" action="{{ route('admin.aclaraciones.rechazar', $aclaracion) }}" class="flex flex-col gap-2">
                        @csrf
                        <label class="text-xs font-semibold text-gray-600">Motivo del rechazo</label>
                        <textarea name="motivo_rechazo" required rows="2"
                                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none"></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="rechazando = false"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600">Cancelar</button>
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl text-xs font-bold bg-red-600 text-white">Confirmar rechazo</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            @endforeach

            @endif

        </div>

    </div>
</section>
@endsection
