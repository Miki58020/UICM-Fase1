@extends('layouts.app')

@section('title', 'Mis documentos | UICM')

@section('content')
<section class="bg-uicm-gray min-h-screen py-12 px-4">
    <div class="container mx-auto px-4 lg:px-12 max-w-5xl">

        <div class="mb-8">
            <a href="{{ route('alumno.dashboard') }}"
               class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4 w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Mi portal
            </a>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">Portal del Alumno</p>
            <h1 class="text-2xl font-extrabold text-gray-900">Mis documentos</h1>
            <p class="text-sm text-gray-500 mt-1">
                Sube o actualiza los documentos de tu expediente. Algunos documentos tienen vigencia y deberás renovarlos cuando venzan.
            </p>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>

        <div class="space-y-4">
            @foreach ($items as $item)
            @php
                $doc = $item['documento'];
                $estado = !$doc ? 'sin_subir' : ($doc->estaVencido() ? 'vencido' : ($doc->porVencer() ? 'por_vencer' : 'vigente'));
                $badge = [
                    'sin_subir'  => ['#9ca3af', 'Sin subir'],
                    'vigente'    => ['#0F4229', 'Vigente'],
                    'por_vencer' => ['#EFAD5A', 'Próximo a vencer'],
                    'vencido'    => ['#dc2626', 'Vencido'],
                ][$estado];
            @endphp
            <div class="bg-white rounded-2xl shadow-md overflow-hidden" x-data="{ subiendo: false }">
                <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>

                <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm">{{ $item['label'] }}</h3>
                        @if ($doc)
                            <p class="text-xs text-gray-400 mt-0.5">
                                Subido el {{ $doc->fecha_subida->format('d/m/Y') }}
                                @if ($doc->fecha_vigencia)
                                    &nbsp;·&nbsp; Vigente hasta {{ $doc->fecha_vigencia->format('d/m/Y') }}
                                @endif
                            </p>
                        @else
                            <p class="text-xs text-gray-400 mt-0.5">Aún no has subido este documento.</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                              style="background-color: {{ $badge[0] }};">
                            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                            {{ $badge[1] }}
                        </span>
                        <button type="button" @click="subiendo = !subiendo"
                                class="text-xs font-bold px-4 py-2 rounded-lg"
                                style="background-color: #0F4229; color: white;">
                            {{ $doc ? 'Reemplazar' : 'Subir' }}
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50" x-show="subiendo" x-cloak>
                    <form method="POST" action="{{ route('alumno.documentos.subir', $item['tipo']) }}"
                          enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                        @csrf
                        <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required
                               class="flex-1 text-sm text-gray-600 border-2 border-dashed border-gray-200 rounded-xl px-4 py-2.5 outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700">
                        <button type="submit"
                                class="px-4 py-2 rounded-lg text-xs font-bold text-white"
                                style="background-color: #D4AF37;">
                            Guardar
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 mt-2">Formatos permitidos: PDF, JPG, PNG. Máximo 5 MB.</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>
@endsection
