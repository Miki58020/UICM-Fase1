@extends('layouts.app')

@section('title', 'Dashboard | UICM')

@section('content')

@php
    $rolLabel = match($rol) {
        'admin'           => 'Administrador',
        'control_escolar' => 'Control Escolar',
        'finanzas'        => 'Finanzas',
        'coordinacion'    => 'Coordinación Académica',
        'profesor'        => 'Profesor',
        default           => ucfirst($rol),
    };

    $user = auth()->user();
    $primerNombre = ucfirst(strtolower(explode(' ', trim($user->name ?? ''))[0]));

    $now = now();
    $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $dias  = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    $fechaHoy = $dias[$now->dayOfWeek] . ', ' . $now->day . ' de ' . $meses[$now->month - 1] . ' de ' . $now->year;
@endphp

<section class="bg-uicm-gray min-h-screen">

    {{-- ══ BANNER ══ --}}
    <div class="w-full px-4 py-8" style="background-color: #0F4229;">
        <div class="container mx-auto px-4 lg:px-12 max-w-7xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

                <div class="flex items-center gap-5">

                    {{-- Avatar con foto de perfil --}}
                    <div x-data="{ hover: false }"
                         class="flex-shrink-0 relative cursor-pointer"
                         @mouseenter="hover = true" @mouseleave="hover = false"
                         @click="$refs.fotoInput.click()"
                         title="Cambiar foto de perfil">

                        <div class="w-20 h-20 rounded-2xl border-2 border-white/30 overflow-hidden">
                            @if(auth()->user()->foto)
                                <img src="{{ route('admin.archivo', ['path' => auth()->user()->foto]) }}"
                                     alt="Foto de perfil"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center
                                            text-3xl font-extrabold text-white select-none"
                                     style="background-color: rgba(255,255,255,0.15);">
                                    {{ strtoupper(substr($primerNombre, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Overlay hover (Alpine.js) --}}
                        <div x-show="hover"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute inset-0 rounded-2xl flex flex-col items-center justify-center gap-1"
                             style="display:none; background-color: rgba(0,0,0,0.58);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-xs font-bold text-white leading-tight text-center">Cambiar<br>foto</span>
                        </div>

                        {{-- Input oculto --}}
                        <form method="POST" action="{{ route('perfil.foto') }}" enctype="multipart/form-data"
                              x-ref="fotoForm" class="hidden">
                            @csrf
                            <input x-ref="fotoInput" type="file" name="foto" accept="image/*"
                                   @change="$refs.fotoForm.submit()">
                        </form>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                            {{ $rolLabel }}
                        </p>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
                            Bienvenido, {{ $primerNombre }}
                        </h1>
                        <p class="text-sm text-white/50 mt-0.5 capitalize">{{ $fechaHoy }}</p>
                    </div>
                </div>

                <span class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold
                             bg-white/15 text-white border border-white/20 self-start sm:self-auto">
                    <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse inline-block"></span>
                    Sesión activa
                </span>

            </div>
        </div>
    </div>

    {{-- ══ CONTENIDO ══ --}}
    <div class="container mx-auto px-4 lg:px-12 max-w-7xl py-8 space-y-6">


        {{-- ════════════════════
             CONTROL ESCOLAR
        ════════════════════ --}}
        @if($rol === 'control_escolar')

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            @php
            $cards = [
                [
                    'label'   => 'Aspirantes pendientes',
                    'value'   => $aspirantesPendientes,
                    'color'   => '#EFAD5A',
                    'bg'      => '#fef4e8',
                    'alerta'  => $aspirantesPendientes > 0,
                    'textoOk' => 'Al día',
                    'textoAl' => 'Requieren revisión',
                    'href'    => route('admin.aspirantes.index'),
                    'icon'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                ],
                [
                    'label'   => 'Listos para inscribir',
                    'value'   => $listosInscribir,
                    'color'   => '#0F4229',
                    'bg'      => '#f0f9f4',
                    'alerta'  => $listosInscribir > 0,
                    'textoOk' => 'Sin pendientes',
                    'textoAl' => 'Pendientes de matrícula',
                    'href'    => route('admin.inscripciones.index'),
                    'icon'    => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4z',
                ],
                [
                    'label'   => 'Alumnos activos',
                    'value'   => $alumnosActivos,
                    'color'   => '#D4AF37',
                    'bg'      => '#fdf8ec',
                    'alerta'  => false,
                    'textoOk' => 'Matriculados',
                    'textoAl' => '',
                    'href'    => route('admin.alumnos.index'),
                    'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                ],
                [
                    'label'   => 'Solicitudes contraseña',
                    'value'   => $solicitudesContrasena,
                    'color'   => $solicitudesContrasena > 0 ? '#dc2626' : '#6B7280',
                    'bg'      => $solicitudesContrasena > 0 ? '#fef2f2' : '#f3f4f6',
                    'alerta'  => $solicitudesContrasena > 0,
                    'textoOk' => 'Sin solicitudes',
                    'textoAl' => 'Pendientes de atender',
                    'href'    => route('admin.solicitudes-contrasena.index', ['tipo' => 'alumnos']),
                    'icon'    => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
                ],
            ];
            @endphp
            @foreach($cards as $card)
            <a href="{{ $card['href'] }}"
               class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: {{ $card['color'] }};"></div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: {{ $card['bg'] }};">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: {{ $card['color'] }};">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                            </svg>
                        </div>
                        @if($card['alerta'])
                        <span class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0" style="background-color: #dc2626;"></span>
                        @endif
                    </div>
                    <p class="text-4xl font-extrabold leading-none mb-1" style="color: #0F4229;">
                        {{ $card['value'] }}
                    </p>
                    <p class="text-xs font-medium text-gray-500 mb-3">{{ $card['label'] }}</p>
                    <div class="mt-auto flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block"
                              style="background-color: {{ $card['alerta'] ? '#dc2626' : '#0F4229' }};"></span>
                        <span class="text-xs font-medium"
                              style="color: {{ $card['alerta'] ? '#dc2626' : '#0F4229' }};">
                            {{ $card['alerta'] ? $card['textoAl'] : $card['textoOk'] }}
                        </span>
                    </div>
                </div>
            </a>
            @endforeach

        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[350px]">
            <div class="h-1.5 w-full flex-shrink-0" style="background-color: #0F4229;"></div>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Últimos aspirantes registrados</h2>
                </div>
                <a href="{{ route('admin.aspirantes.index') }}" class="text-xs font-semibold hover:underline" style="color:#0F4229;">
                    Ver todos →
                </a>
            </div>
            <div class="overflow-auto flex-1">
                @if($ultimosAspirantes->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">No hay aspirantes registrados aún.</div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Programa</th>
                            <th class="px-6 py-3">Folio</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($ultimosAspirantes as $asp)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">
                            <td class="px-6 py-3.5 font-medium text-gray-800">
                                {{ $asp->nombre }} {{ $asp->apellido_paterno }}
                            </td>
                            <td class="px-6 py-3.5 text-gray-500 text-xs">{{ $asp->programa->nombre ?? '—' }}</td>
                            <td class="px-6 py-3.5 font-mono text-xs text-gray-400">{{ $asp->folio }}</td>
                            <td class="px-6 py-3.5 text-center">
                                @php
                                    $bc = match($asp->estado) {
                                        'aprobado'  => ['#dcfce7','#0F4229'],
                                        'rechazado' => ['#fee2e2','#dc2626'],
                                        default     => ['#fef4e8','#b45309'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold capitalize"
                                      style="background-color:{{ $bc[0] }};color:{{ $bc[1] }};">
                                    {{ $asp->estado }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>


        {{-- ════════════════════
             FINANZAS
        ════════════════════ --}}
        @elseif($rol === 'finanzas')

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            @php
            $fCards = [
                ['label'=>'Pagos por revisar',  'value'=>$pagosPendientes, 'color'=>'#EFAD5A','bg'=>'#fef4e8', 'alerta'=>$pagosPendientes>0, 'textoOk'=>'Al día','textoAl'=>'Requieren atención',
                 'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label'=>'Pagos aprobados',    'value'=>$pagosAprobados,  'color'=>'#0F4229','bg'=>'#f0f9f4', 'alerta'=>false,'textoOk'=>'Confirmados','textoAl'=>'',
                 'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label'=>'Pagos rechazados',   'value'=>$pagosRechazados, 'color'=>'#6B7280','bg'=>'#f3f4f6', 'alerta'=>false,'textoOk'=>'No aceptados','textoAl'=>'',
                 'icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label'=>'Monto recaudado MXN','value'=>'$'.number_format($montoTotal,0),'color'=>'#D4AF37','bg'=>'#fdf8ec','alerta'=>false,'textoOk'=>'Pagos confirmados','textoAl'=>'',
                 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
            @endphp
            @foreach($fCards as $card)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: {{ $card['color'] }};"></div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: {{ $card['bg'] }};">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: {{ $card['color'] }};">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                            </svg>
                        </div>
                        @if($card['alerta'])
                        <span class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0 inline-block" style="background-color: #dc2626;"></span>
                        @endif
                    </div>
                    <p class="text-4xl font-extrabold leading-none mb-1" style="color: #0F4229;">
                        {{ $card['value'] }}
                    </p>
                    <p class="text-xs font-medium text-gray-500 mb-3">{{ $card['label'] }}</p>
                    <div class="mt-auto flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block"
                              style="background-color: {{ $card['alerta'] ? '#dc2626' : $card['color'] }};"></span>
                        <span class="text-xs font-medium"
                              style="color: {{ $card['alerta'] ? '#dc2626' : $card['color'] }};">
                            {{ $card['alerta'] ? $card['textoAl'] : $card['textoOk'] }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[350px]">
            <div class="h-1.5 w-full flex-shrink-0" style="background-color: #D4AF37;"></div>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#D4AF37;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Últimos comprobantes recibidos</h2>
                </div>
                <a href="{{ route('finanzas.pagos.index') }}" class="text-xs font-semibold hover:underline" style="color:#0F4229;">
                    Ver todos →
                </a>
            </div>
            <div class="overflow-auto flex-1">
                @if($ultimosPagos->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">No hay pagos registrados aún.</div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Aspirante</th>
                            <th class="px-6 py-3">Concepto</th>
                            <th class="px-6 py-3">Monto</th>
                            <th class="px-6 py-3">Fecha</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $labelsConcepto = [
                                'inscripcion'  => 'Inscripción',
                                'colegiatura'  => 'Colegiatura',
                                'cuatrimestre' => 'Reinscripción',
                                'otro'         => 'Otro',
                            ];
                        @endphp
                        @foreach($ultimosPagos as $pago)
                        <tr class="hover:bg-gray-50 transition-colors duration-100">
                            <td class="px-6 py-3.5 font-medium text-gray-800">
                                {{ $pago->aspirante?->nombre_completo ?? $pago->alumno?->nombre_completo ?? '—' }}
                            </td>
                            <td class="px-6 py-3.5 text-gray-500">{{ $labelsConcepto[$pago->concepto] ?? ucfirst($pago->concepto) }}</td>
                            <td class="px-6 py-3.5 font-bold text-gray-800">${{ number_format($pago->monto, 0) }} MXN</td>
                            <td class="px-6 py-3.5 text-xs text-gray-400">
                                {{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : '—' }}
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                @php
                                    $pc = match($pago->estado) {
                                        'aprobado'  => ['#dcfce7','#0F4229','Pagado'],
                                        'rechazado' => ['#f3f4f6','#6B7280','Rechazado'],
                                        default     => ['#fef4e8','#b45309','En revisión'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                      style="background-color:{{ $pc[0] }};color:{{ $pc[1] }};">
                                    {{ $pc[2] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>


        {{-- ════════════════════
             COORDINACIÓN
        ════════════════════ --}}
        @elseif($rol === 'coordinacion')

        @if($periodoActivo)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #D4AF37;"></div>
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color:#fdf8ec;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#D4AF37;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Periodo activo</p>
                        <p class="text-base font-bold" style="color: #0F4229;">{{ $periodoActivo->nombre }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.periodos.index') }}"
                   class="text-xs font-semibold px-3 py-2 rounded-lg text-white transition-colors"
                   style="background-color:#0F4229;"
                   onmouseover="this.style.backgroundColor='#0a2e1c'"
                   onmouseout="this.style.backgroundColor='#0F4229'">
                    Gestionar periodos
                </a>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
            $cCards = [
                ['label'=>'Grupos registrados','value'=>$totalGrupos,    'color'=>'#0F4229','bg'=>'#f0f9f4',
                 'alerta'=>$gruposSinCarga>0,'textoOk'=>'Carga completa','textoAl'=>$gruposSinCarga.' sin carga asignada',
                 'href'=>route('admin.grupos.index'),
                 'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label'=>'Profesores activos','value'=>$totalProfesores,'color'=>'#D4AF37','bg'=>'#fdf8ec',
                 'alerta'=>false,'textoOk'=>'Planta docente','textoAl'=>'',
                 'href'=>route('admin.profesores.index'),
                 'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['label'=>'Materias activas',  'value'=>$totalMaterias,  'color'=>'#EFAD5A','bg'=>'#fef4e8',
                 'alerta'=>false,'textoOk'=>'En oferta académica','textoAl'=>'',
                 'href'=>route('admin.materias.index'),
                 'icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['label'=>'Grupos sin carga',  'value'=>$gruposSinCarga, 'color'=>$gruposSinCarga>0?'#dc2626':'#0F4229','bg'=>$gruposSinCarga>0?'#fef2f2':'#f0f9f4',
                 'alerta'=>$gruposSinCarga>0,'textoOk'=>'Todo asignado','textoAl'=>'Pendientes de asignar',
                 'href'=>route('admin.carga-academica.index'),
                 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
            ];
            @endphp
            @foreach($cCards as $card)
            <a href="{{ $card['href'] }}"
               class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: {{ $card['color'] }};"></div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: {{ $card['bg'] }};">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: {{ $card['color'] }};">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                            </svg>
                        </div>
                        @if($card['alerta'])
                        <span class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0 inline-block" style="background-color:#dc2626;"></span>
                        @endif
                    </div>
                    <p class="text-4xl font-extrabold leading-none mb-1" style="color: #0F4229;">{{ $card['value'] }}</p>
                    <p class="text-xs font-medium text-gray-500 mb-3">{{ $card['label'] }}</p>
                    <div class="mt-auto flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block"
                              style="background-color: {{ $card['alerta'] ? '#dc2626' : '#0F4229' }};"></span>
                        <span class="text-xs font-medium"
                              style="color: {{ $card['alerta'] ? '#dc2626' : '#0F4229' }};">
                            {{ $card['alerta'] ? $card['textoAl'] : $card['textoOk'] }}
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Grupos recientes --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[350px]">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: #0F4229;"></div>
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-gray-700">Grupos recientes</h2>
                    </div>
                    <a href="{{ route('admin.grupos.index') }}" class="text-xs font-semibold hover:underline" style="color:#0F4229;">
                        Ver todos →
                    </a>
                </div>
                <div class="overflow-auto flex-1">
                    @if($ultimosGrupos->isEmpty())
                        <div class="px-6 py-10 text-center text-sm text-gray-400">No hay grupos registrados aún.</div>
                    @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Clave</th>
                                <th class="px-6 py-3">Programa</th>
                                <th class="px-6 py-3 text-center">Cuatri.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($ultimosGrupos as $grupo)
                            <tr class="hover:bg-gray-50 transition-colors duration-100">
                                <td class="px-6 py-3.5 font-mono font-bold text-xs" style="color:#0F4229;">{{ $grupo->clave }}</td>
                                <td class="px-6 py-3.5 text-gray-600 text-xs truncate max-w-[140px]">{{ $grupo->programa->nombre ?? '—' }}</td>
                                <td class="px-6 py-3.5 text-center text-xs font-bold" style="color:#EFAD5A;">
                                    {{ $grupo->cuatrimestre }}°
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>

            {{-- Grupos por cuatrimestre --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[350px]">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: #EFAD5A;"></div>
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#EFAD5A;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-gray-700">Grupos por cuatrimestre</h2>
                    </div>
                    <span class="text-xs text-gray-400">{{ $totalPeriodos }} periodo(s) registrado(s)</span>
                </div>
                <div class="px-6 py-5 flex flex-col flex-1">
                    @if($gruposPorCuatrimestre->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-6">Sin grupos registrados aún.</p>
                    @else
                        <p class="text-xs text-gray-400 mb-3">Semestre del programa académico</p>
                        <div class="space-y-2 overflow-y-auto">
                            @foreach($gruposPorCuatrimestre->sortKeys() as $cuatri => $count)
                            <div class="flex items-center justify-between px-3 py-2.5 rounded-xl" style="background-color:#fef4e8;">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-extrabold flex-shrink-0 text-white"
                                          style="background-color:#EFAD5A;">{{ $cuatri }}</span>
                                    <span class="text-sm text-gray-600">{{ $cuatri }}° semestre</span>
                                </div>
                                <span class="text-sm font-extrabold" style="color:#0F4229;">
                                    {{ $count }} {{ $count === 1 ? 'grupo' : 'grupos' }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-gray-400">Total de grupos registrados</span>
                            <span class="text-sm font-extrabold" style="color:#0F4229;">{{ $totalGrupos }}</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>


        {{-- ════════════════════
             PROFESOR
        ════════════════════ --}}
        @elseif($rol === 'profesor')

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            @php
            $pCards = [
                ['label'=>'Materias', 'value'=>$materiasProfesor, 'color'=>'#0F4229','bg'=>'#f0f9f4','alerta'=>false,'textoOk'=>'Asignadas','textoAl'=>'',
                 'href'=>route('profesor.calificaciones.index'),
                 'icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['label'=>'Grupos', 'value'=>$gruposProfesor, 'color'=>'#D4AF37','bg'=>'#fdf8ec','alerta'=>false,'textoOk'=>'A tu cargo','textoAl'=>'',
                 'href'=>route('profesor.grupos.index'),
                 'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label'=>'Alumnos', 'value'=>$alumnosProfesor, 'color'=>'#EFAD5A','bg'=>'#fef4e8','alerta'=>false,'textoOk'=>'Activos','textoAl'=>'',
                 'href'=>route('profesor.alumnos.index'),
                 'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['label'=>'Por calificar', 'value'=>$porCalificarProfesor,
                 'color'=>$porCalificarProfesor>0?'#dc2626':'#6B7280','bg'=>$porCalificarProfesor>0?'#fef2f2':'#f3f4f6',
                 'alerta'=>$porCalificarProfesor>0,'textoOk'=>'Al día','textoAl'=>'Requieren atención',
                 'href'=>route('profesor.calificaciones.index'),
                 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                ['label'=>'Aclaraciones', 'value'=>$aclaracionesPendientes,
                 'color'=>$aclaracionesPendientes>0?'#dc2626':'#6B7280','bg'=>$aclaracionesPendientes>0?'#fef2f2':'#f3f4f6',
                 'alerta'=>$aclaracionesPendientes>0,'textoOk'=>'Sin pendientes','textoAl'=>'Pendientes de revisión',
                 'href'=>route('profesor.aclaraciones.index'),
                 'icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4v-4z'],
            ];
            @endphp
            @foreach($pCards as $card)
            <a href="{{ $card['href'] }}"
               class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: {{ $card['color'] }};"></div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: {{ $card['bg'] }};">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: {{ $card['color'] }};">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                            </svg>
                        </div>
                        @if($card['alerta'])
                        <span class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0 inline-block" style="background-color:#dc2626;"></span>
                        @endif
                    </div>
                    <p class="text-4xl font-extrabold leading-none mb-1" style="color: #0F4229;">{{ $card['value'] }}</p>
                    <p class="text-xs font-medium text-gray-500 mb-3">{{ $card['label'] }}</p>
                    <div class="mt-auto flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block"
                              style="background-color: {{ $card['alerta'] ? '#dc2626' : '#0F4229' }};"></span>
                        <span class="text-xs font-medium"
                              style="color: {{ $card['alerta'] ? '#dc2626' : '#0F4229' }};">
                            {{ $card['alerta'] ? $card['textoAl'] : $card['textoOk'] }}
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[350px]">
            <div class="h-1.5 w-full flex-shrink-0" style="background-color: #0F4229;"></div>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0F4229;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700">Grupos por atender</h2>
            </div>
            <div class="overflow-auto flex-1">
                @if($gruposPorAtender->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">No tienes grupos pendientes de calificar. ¡Vas al día!</div>
                @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Materia</th>
                            <th class="px-6 py-3">Grupo</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($gruposPorAtender as $carga)
                        @php
                            $estadoBadge = match($carga->estado_revision) {
                                'rechazado' => ['#fee2e2','#dc2626','Rechazado'],
                                default     => ['#f3f4f6','#6B7280','Sin enviar'],
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-100">
                            <td class="px-6 py-3.5 font-medium text-gray-800 truncate max-w-[160px]">{{ $carga->materia->nombre ?? '—' }}</td>
                            <td class="px-6 py-3.5 font-mono text-xs text-gray-500">{{ $carga->grupo->clave ?? '—' }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                      style="background-color:{{ $estadoBadge[0] }};color:{{ $estadoBadge[1] }};">
                                    {{ $estadoBadge[2] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>


        {{-- ════════════════════
             ADMIN
        ════════════════════ --}}
        @else

        {{-- KPI cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
            $aCards = [
                ['label'=>'Usuarios del sistema',  'value'=>$totalUsuariosSistema,
                 'color'=>'#0F4229','bg'=>'#f0f9f4','alerta'=>false,
                 'textoOk'=>'Personal activo','textoAl'=>'',
                 'href'=>route('admin.usuarios.index'),
                 'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['label'=>'Aspirantes en proceso', 'value'=>$aspirantesEnProceso,
                 'color'=>'#D4AF37','bg'=>'#fdf8ec','alerta'=>$aspirantesEnProceso>0,
                 'textoOk'=>'Sin pendientes','textoAl'=>'Requieren atención',
                 'href'=>route('admin.aspirantes.index'),
                 'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['label'=>'Mensajes sin atender',  'value'=>$mensajesPendientes,
                 'color'=>$mensajesPendientes>0?'#EFAD5A':'#6B7280',
                 'bg'=>$mensajesPendientes>0?'#fef4e8':'#f3f4f6',
                 'alerta'=>$mensajesPendientes>0,'textoOk'=>'Todos atendidos','textoAl'=>'Pendientes de respuesta',
                 'href'=>route('admin.contactos.index'),
                 'icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['label'=>'Solicitudes contraseña','value'=>$solicitudesPendientes,
                 'color'=>$solicitudesPendientes>0?'#dc2626':'#6B7280',
                 'bg'=>$solicitudesPendientes>0?'#fef2f2':'#f3f4f6',
                 'alerta'=>$solicitudesPendientes>0,'textoOk'=>'Sin solicitudes','textoAl'=>'Pendientes de atender',
                 'href'=>route('admin.solicitudes-contrasena.index'),
                 'icon'=>'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
            ];
            @endphp
            @foreach($aCards as $card)
            <a href="{{ $card['href'] }}"
               class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: {{ $card['color'] }};"></div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: {{ $card['bg'] }};">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: {{ $card['color'] }};">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                            </svg>
                        </div>
                        @if($card['alerta'])
                        <span class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0 inline-block" style="background-color:#dc2626;"></span>
                        @endif
                    </div>
                    <p class="text-4xl font-extrabold leading-none mb-1" style="color: #0F4229;">{{ $card['value'] }}</p>
                    <p class="text-xs font-medium text-gray-500 mb-3">{{ $card['label'] }}</p>
                    <div class="mt-auto flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 inline-block"
                              style="background-color: {{ $card['alerta'] ? '#dc2626' : '#0F4229' }};"></span>
                        <span class="text-xs font-medium"
                              style="color: {{ $card['alerta'] ? '#dc2626' : '#0F4229' }};">
                            {{ $card['alerta'] ? $card['textoAl'] : $card['textoOk'] }}
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pendientes por área --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0F4229;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700">Resumen de pendientes por área</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
                @foreach($deptos as $depto)
                @php
                    $hayPendientes = $depto['total'] > 0;
                    $rolKey = $depto['rolKey'];
                @endphp
                <div class="p-5 flex flex-col h-full">

                    {{-- Contenido superior (crece para llenar espacio disponible) --}}
                    <div class="flex flex-col gap-3 flex-1 mb-3">

                        {{-- Encabezado (enlace al módulo) --}}
                        <a href="{{ $depto['href'] }}" class="flex items-center justify-between hover:opacity-80 transition-opacity">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 inline-block"
                                      style="background-color: {{ $hayPendientes ? '#dc2626' : '#0F4229' }};"></span>
                                <span class="text-xs font-bold text-gray-700">{{ $depto['label'] }}</span>
                            </div>
                            <span class="text-xl font-extrabold {{ $hayPendientes ? 'text-red-600' : 'text-gray-300' }}">
                                {{ $depto['total'] }}
                            </span>
                        </a>

                        {{-- Desglose --}}
                        <div class="space-y-1.5">
                            @foreach($depto['items'] as $item)
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 truncate pr-2">{{ $item['label'] }}</span>
                                <span class="text-xs font-bold flex-shrink-0 px-2 py-0.5 rounded-full"
                                      style="background-color: {{ $item['count'] > 0 ? '#fef2f2' : '#f3f4f6' }};
                                             color: {{ $item['count'] > 0 ? '#dc2626' : '#9CA3AF' }};">
                                    {{ $item['count'] }}
                                </span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Estado --}}
                        <p class="text-xs mt-auto {{ $hayPendientes ? 'text-red-500 font-semibold' : 'text-green-600 font-medium' }}">
                            {{ $hayPendientes ? 'Requiere atención' : 'Al día ✓' }}
                        </p>

                        </div>

                    {{-- Botón notificar — siempre al fondo --}}
                    <form method="POST" action="{{ route('admin.notificar.departamento', $rolKey) }}"
                          x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        <button type="submit"
                                :disabled="loading"
                                class="w-full flex items-center justify-center gap-1.5 text-xs font-semibold py-2 px-3 rounded-xl border-2 transition-all duration-150"
                                :class="loading ? 'opacity-50 cursor-not-allowed' : ''"
                                style="border-color: {{ $depto['color'] }}; color: {{ $depto['color'] }}; background-color: transparent;"
                                x-on:mouseenter="if (!loading) $el.style.backgroundColor = '{{ $depto['bgIcon'] }}'"
                                x-on:mouseleave="$el.style.backgroundColor = 'transparent'">
                            <template x-if="!loading">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    Notificar
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                    </svg>
                                    Enviando…
                                </span>
                            </template>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Fila inferior: distribución + usuarios recientes --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Distribución por rol --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[350px]">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: #0F4229;"></div>
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2 flex-shrink-0">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0F4229;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Distribución por rol</h2>
                </div>
                <div class="px-6 py-5 flex flex-col justify-around gap-4 overflow-auto flex-1">
                    @php
                        $rolDef = [
                            'admin'           => ['Administrador',   '#0F4229'],
                            'control_escolar' => ['Control Escolar', '#D4AF37'],
                            'finanzas'        => ['Finanzas',        '#EFAD5A'],
                            'coordinacion'    => ['Coordinación',    '#1F5FBF'],
                        ];
                        $totalU = max($usuariosPorRol->sum(), 1);
                    @endphp
                    @foreach($rolDef as $key => [$nombre, $color])
                    @php $cnt = $usuariosPorRol[$key] ?? 0; $pct = round(($cnt / $totalU) * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-gray-600">{{ $nombre }}</span>
                            <span class="text-xs font-bold" style="color: #0F4229;">{{ $cnt }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full"
                                 style="width: {{ $pct }}%; background-color: {{ $color }};"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Usuarios recientes --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[350px]">
                <div class="h-1.5 w-full flex-shrink-0" style="background-color: #D4AF37;"></div>
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#D4AF37;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-gray-700">Usuarios recientes</h2>
                    </div>
                    <a href="{{ route('admin.usuarios.index') }}" class="text-xs font-semibold hover:underline" style="color:#0F4229;">
                        Ver todos →
                    </a>
                </div>
                <div class="divide-y divide-gray-100 overflow-auto flex-1">
                    @forelse($ultimosUsuarios as $u)
                    @php
                        $rb = match($u->rol) {
                            'admin'           => ['#f0f9f4','#0F4229','Admin'],
                            'control_escolar' => ['#fdf8ec','#b45309','Control Esc.'],
                            'finanzas'        => ['#fef4e8','#b45309','Finanzas'],
                            'coordinacion'    => ['#eaf0fb','#1F5FBF','Coordinación'],
                            'profesor'        => ['#f3f4f6','#4B5563','Profesor'],
                            default           => ['#f3f4f6','#4B5563', ucfirst($u->rol)],
                        };
                    @endphp
                    <div class="px-6 py-3.5 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $u->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $u->email }}</p>
                        </div>
                        <span class="inline-flex items-center flex-shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                              style="background-color:{{ $rb[0] }};color:{{ $rb[1] }};">
                            {{ $rb[2] }}
                        </span>
                    </div>
                    @empty
                    <div class="px-6 py-10 text-center text-sm text-gray-400">Sin usuarios registrados.</div>
                    @endforelse
                </div>
            </div>

        </div>

        @endif

    </div>
</section>

@endsection
