<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Universidad Internacional Cuba México')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ===== SEGURIDAD DE SESIÓN POR PESTAÑA ===== --}}
    {{-- Ejecuta antes de renderizar el contenido para evitar exponer datos si la sesión es inválida --}}
    @auth
    <script>
    (function () {
        var KEY = 'uicm_tab_alive';

        @if(session('uicm_init'))
        {{-- Acceso recién autenticado: inicializar la marca de pestaña activa --}}
        sessionStorage.setItem(KEY, '1');
        @else
        {{-- Verificar si esta pestaña tiene una sesión activa marcada --}}
        if (!sessionStorage.getItem(KEY)) {
            {{-- No hay marca: pestaña cerrada y restaurada, o ventana nueva no autorizada --}}
            document.documentElement.style.display = 'none';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/session/terminate', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onloadend = function () { window.location.replace('/login'); };
            xhr.onerror   = function () { window.location.replace('/login'); };
            xhr.send();
        }
        @endif
    })();
    </script>
    @endauth
</head>
<body class="bg-uicm-gray font-sans antialiased text-gray-800" x-data="{ sidebarOpen: false }">

    {{-- ===== NAVBAR ===== --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-md" id="main-navbar">
        <div class="px-4 lg:px-6">
            <div class="relative flex items-center justify-between h-16">

                {{-- Lado izquierdo: toggle (mobile) + logo --}}
                <div class="flex items-center gap-3">

                    @auth
                    {{-- Botón hamburguesa sidebar — solo móvil --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="md:hidden inline-flex items-center justify-center w-9 h-9 rounded-lg
                                   text-white transition-colors duration-150"
                            style="background-color: #0F4229;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    @endauth

                    @auth
                    <form method="POST" action="{{ route('logout') }}"
                          onsubmit="localStorage.setItem('uicm_logout', Date.now()); sessionStorage.removeItem('uicm_tab_alive');">
                        @csrf
                        <button type="submit" class="p-0 border-0 bg-transparent cursor-pointer">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo UICM" class="h-10 w-auto">
                        </button>
                    </form>
                    @else
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo UICM" class="h-10 w-auto">
                    </a>
                    @endauth
                </div>

                {{-- Rol y nombre del usuario autenticado --}}
                @auth
                @php
                    $rolLabel = match(auth()->user()->rol) {
                        'admin'           => 'ADMINISTRADOR',
                        'control_escolar' => 'CONTROL ESCOLAR',
                        'finanzas'        => 'FINANZAS',
                        'coordinacion'    => 'COORDINACIÓN',
                        'alumno'          => 'ALUMNO',
                        default           => strtoupper(auth()->user()->rol),
                    };
                    // Normalizar: eliminar acentos y convertir a mayúsculas
                    $norm = function (?string $t): string {
                        if (!$t) return '';
                        $t = normalizer_normalize($t, Normalizer::FORM_D);
                        $t = preg_replace('/\p{Mn}/u', '', $t);
                        return strtoupper($t);
                    };
                    // Solo primer nombre + apellido paterno
                    if (auth()->user()->rol === 'alumno' && auth()->user()->alumno) {
                        $primerNombre    = explode(' ', trim(auth()->user()->alumno->nombre ?? ''))[0];
                        $apellidoPaterno = auth()->user()->alumno->apellido_paterno ?? '';
                    } else {
                        $primerNombre    = explode(' ', trim(auth()->user()->name ?? ''))[0];
                        $apellidoPaterno = auth()->user()->apellido_paterno ?? '';
                    }
                    $nombreMostrado = trim($norm($primerNombre) . ' ' . $norm($apellidoPaterno));
                @endphp
                <span class="hidden md:block absolute left-64 pl-6 text-sm font-medium text-gray-600">
                    <span class="font-bold" style="color: #0F4229;">{{ $rolLabel }}</span>
                    <span class="text-gray-400 mx-1">—</span>
                    {{ $nombreMostrado }}
                </span>
                @endauth

                {{-- Lado derecho --}}
                @auth
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('logout') }}"
                          onsubmit="localStorage.setItem('uicm_logout', Date.now()); sessionStorage.removeItem('uicm_tab_alive');">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold
                                       text-white transition-colors duration-200"
                                style="background-color: #0F4229;"
                                onmouseover="this.style.backgroundColor='#0a2e1c'"
                                onmouseout="this.style.backgroundColor='#0F4229'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7
                                         a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="hidden sm:inline">Cerrar sesión</span>
                        </button>
                    </form>
                </div>
                @else
                {{-- Navbar público --}}
                @unless(request()->routeIs('login'))
                <button id="mobile-menu-btn"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600
                               hover:text-uicm-green hover:bg-gray-100 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="hamburger-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-uicm-green hover:text-green-800 transition-colors">Inicio</a>
                    <a href="{{ route('home') }}#oferta" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Oferta educativa</a>
                    <a href="{{ route('aspirantes.registro') }}" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Inscripción</a>
                    <a href="{{ route('aspirantes.seguimiento') }}" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Consultar estatus</a>
                    <a href="{{ route('home') }}#contacto" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Contáctanos</a>
                    <a href="{{ route('login') }}"
                       class="text-sm font-semibold text-white px-4 py-2 rounded-lg transition-colors duration-150"
                       style="background-color: #0F4229;"
                       onmouseover="this.style.backgroundColor='#0a2e1c'"
                       onmouseout="this.style.backgroundColor='#0F4229'">
                        Portal
                    </a>
                </div>
                @endunless
                @endauth

            </div>
        </div>

        {{-- Menú móvil público --}}
        @guest
        @unless(request()->routeIs('login'))
        <div id="nav-menu" class="hidden md:hidden bg-white border-t border-gray-100 px-4 pb-4">
            <div class="flex flex-col gap-3 pt-3">
                <a href="{{ route('home') }}" class="text-base font-medium text-uicm-green">Inicio</a>
                <a href="{{ route('home') }}#oferta" class="text-base font-medium text-gray-600 hover:text-uicm-green">Oferta educativa</a>
                <a href="{{ route('aspirantes.registro') }}" class="text-base font-medium text-gray-600 hover:text-uicm-green">Inscripción</a>
                <a href="{{ route('aspirantes.seguimiento') }}" class="text-base font-medium text-gray-600 hover:text-uicm-green">Consultar estatus</a>
                <a href="{{ route('home') }}#contacto" class="text-base font-medium text-gray-600 hover:text-uicm-green">Contáctanos</a>
                <a href="{{ route('login') }}" class="text-base font-semibold text-white px-4 py-2 rounded-lg text-center"
                   style="background-color: #0F4229;">Portal</a>
            </div>
        </div>
        @endunless
        @endguest
    </nav>

    {{-- Espaciado para el navbar fijo --}}
    <div class="h-16"></div>

    @auth
    {{-- ===== SIDEBAR ===== --}}

    {{-- Overlay móvil --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-black/40 md:hidden"
         style="display: none;">
    </div>

    <aside class="fixed left-0 top-16 bottom-0 z-40 w-64 overflow-y-auto
                  transition-transform duration-300 ease-in-out flex flex-col"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           style="background-color: #0F4229;">

        {{-- Nombre de usuario --}}
        <div class="px-4 py-3 border-b border-white/10 text-center">
            <p class="text-xs text-green-400 uppercase tracking-widest mb-0.5">Bienvenido</p>
            <p class="text-base font-semibold text-white truncate">{{ $nombreMostrado }}</p>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 px-3 py-3">

            @php $rol = auth()->user()->rol; @endphp

            {{-- ══ ADMIN ══ --}}
            @if($rol === 'admin')
            <div class="pt-1 pb-1 px-2">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Administración</p>
            </div>

            <a href="{{ route('admin.usuarios.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.usuarios.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Usuarios del sistema
            </a>

            @php
                $pendientesAdmins = \App\Models\SolicitudContrasena::where('estado', 'pendiente')
                    ->whereHas('user', fn($u) => $u->where('rol', '!=', 'alumno'))
                    ->count();
            @endphp
            <a href="{{ route('admin.solicitudes-contrasena.index', ['tipo' => 'administrativos']) }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.solicitudes-contrasena.*') && request()->query('tipo') === 'administrativos' ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                             a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                <span class="flex-1">Contraseñas</span>
                @if($pendientesAdmins > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                          style="background-color: #dc2626; color: #fff;">
                        {{ $pendientesAdmins }}
                    </span>
                @endif
            </a>
            @endif

            {{-- ══ COORDINACIÓN ACADÉMICA ══ --}}
            @if($rol === 'coordinacion' || $rol === 'admin')
            <div class="mt-4 border-t border-white/10 pt-3 pb-1 px-2">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Coordinación Académica</p>
            </div>

            <a href="{{ route('admin.materias.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.materias.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                             C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                             C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                             C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Materias
            </a>

            <a href="{{ route('admin.profesores.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.profesores.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profesores
            </a>

            <a href="{{ route('admin.carga-academica.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.carga-academica.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5
                             a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Carga académica
            </a>
            @endif

            {{-- ══ CONTROL ESCOLAR ══ --}}
            @if($rol === 'control_escolar' || $rol === 'admin')
            <div class="mt-4 border-t border-white/10 pt-3 pb-1 px-2">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Control Escolar</p>
            </div>

            <a href="{{ route('admin.aspirantes.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.aspirantes.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                             a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Aspirantes
            </a>

            <a href="{{ route('admin.inscripciones.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.inscripciones.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5
                             m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4z"/>
                </svg>
                Inscripciones
            </a>

            <a href="{{ route('admin.alumnos.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.alumnos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                             M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                             m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Alumnos
            </a>

            @php
                $pendientesAlumnos = \App\Models\SolicitudContrasena::where('estado', 'pendiente')
                    ->whereHas('user', fn($u) => $u->where('rol', 'alumno'))
                    ->count();
            @endphp
            <a href="{{ route('admin.solicitudes-contrasena.index', ['tipo' => 'alumnos']) }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.solicitudes-contrasena.*') && request()->query('tipo', 'alumnos') === 'alumnos' ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                             a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                <span class="flex-1">Contraseñas</span>
                @if($pendientesAlumnos > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                          style="background-color: #dc2626; color: #fff;">
                        {{ $pendientesAlumnos }}
                    </span>
                @endif
            </a>
            @endif

            {{-- ══ FINANZAS ══ --}}
            @if($rol === 'finanzas' || $rol === 'admin')
            <div class="mt-4 border-t border-white/10 pt-3 pb-1 px-2">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Finanzas</p>
            </div>

            <a href="{{ route('finanzas.pagos.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('finanzas.pagos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10
                             a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Validación de pagos
            </a>
            @endif

            {{-- ══ ALUMNO ══ --}}
            @if($rol === 'alumno')
            <div class="pt-1 pb-1 px-2">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Portal del alumno</p>
            </div>

            <a href="{{ route('alumno.dashboard') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('alumno.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012
                             20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                Mi portal
            </a>
            @endif

        </nav>


    </aside>
    @endauth

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    <div class="{{ auth()->check() ? 'md:ml-64' : '' }}">
        <main>
            @yield('content')
        </main>

        {{-- ===== FOOTER ===== --}}
        <footer class="text-white text-center py-3 border-t border-green-800" style="background-color: #0F4229;">
            <div class="container mx-auto px-8">
                <p class="font-semibold text-white text-base tracking-wide">
                    Universidad Internacional Cuba México
                    <span class="mx-2 opacity-40">|</span>
                    &copy; {{ date('Y') }} Todos los derechos reservados.
                </p>
            </div>
        </footer>
    </div>

    {{-- Script menú móvil público --}}
    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('nav-menu');

        if (btn && menu) {
            const hamburger = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon');

            btn.addEventListener('click', () => {
                const isOpen = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden');
                if (hamburger) hamburger.classList.toggle('hidden', !isOpen ? true : false);
                if (closeIcon) closeIcon.classList.toggle('hidden', !isOpen ? false : true);
            });
        }

        // Sombra del navbar al hacer scroll
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('main-navbar');
            if (navbar) {
                navbar.classList.toggle('shadow-lg', window.scrollY > 10);
            }
        });
    </script>

    @stack('scripts')

    {{-- ===== SINCRONIZACIÓN DE CIERRE DE SESIÓN ENTRE PESTAÑAS ===== --}}
    @auth
    <script>
    (function () {
        var KEY = 'uicm_tab_alive';

        {{-- Marcar la pestaña como activa en cada página navegada --}}
        sessionStorage.setItem(KEY, '1');

        {{-- Escuchar cierre de sesión disparado por OTRA pestaña --}}
        window.addEventListener('storage', function (e) {
            if (e.key === 'uicm_logout' && e.newValue) {
                sessionStorage.removeItem(KEY);
                window.location.replace('/login');
            }
        });
    })();
    </script>
    @endauth

    @guest
    <script>
    {{-- En páginas públicas/login: limpiar la marca de sesión activa --}}
    sessionStorage.removeItem('uicm_tab_alive');
    </script>
    @endguest
</body>
</html>
