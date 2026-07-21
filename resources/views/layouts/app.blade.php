<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Universidad Internacional Cuba México')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    @keyframes toastIn {
        from { transform: translateX(380px); opacity: 0; }
        to   { transform: translateX(0);     opacity: 1; }
    }
    .toast-item {
        animation: toastIn 0.35s cubic-bezier(0.34, 1.4, 0.64, 1) both;
        box-sizing: border-box;
    }
    .toast-item.toast-out {
        animation: none !important;
        transition: transform 0.28s ease-in, opacity 0.28s ease-in !important;
        transform: translateX(380px) !important;
        opacity: 0 !important;
    }
    @media (max-width: 480px) {
        #toast-container {
            left: 0.75rem !important;
            right: 0.75rem !important;
            width: auto !important;
            top: 0.75rem !important;
        }
        @keyframes toastIn {
            from { transform: translateY(-80px); opacity: 0; }
            to   { transform: translateY(0);     opacity: 1; }
        }
        .toast-item.toast-out {
            transform: translateY(-80px) !important;
        }
    }
    /* Sidebar colapso escritorio */
    .sidebar-anim { transition: width 0.3s ease-in-out, transform 0.3s ease-in-out; }
    .main-anim    { transition: margin-left 0.3s ease-in-out; }

    /* Scroll del sidebar más discreto */
    .sidebar-anim { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.18) transparent; }
    .sidebar-anim::-webkit-scrollbar { width: 6px; }
    .sidebar-anim::-webkit-scrollbar-track { background: transparent; }
    .sidebar-anim::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.18); border-radius: 9999px; }
    .sidebar-anim::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.32); }
    @media (min-width: 768px) {
        aside.sidebar-collapsed-md { width: 4rem !important; }
        .main-auth { margin-left: 16rem; }
        .main-auth.sidebar-collapsed-md { margin-left: 4rem; }
        .sidebar-collapsed-md .nav-link {
            justify-content: center;
            padding-left: 0.375rem;
            padding-right: 0.375rem;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            position: relative;
        }
        .sidebar-collapsed-md .nav-link svg {
            width: 1.5rem !important;
            height: 1.5rem !important;
        }
        .sidebar-collapsed-md .nav-link-text,
        .sidebar-collapsed-md .nav-section-label,
        .sidebar-collapsed-md .sidebar-user-block { display: none; }
        .sidebar-collapsed-md .nav-badge {
            display: block !important;
            position: absolute;
            top: 9px;
            right: 0px;
            width: 9px !important;
            height: 9px !important;
            min-width: 0 !important;
            border-radius: 50% !important;
            padding: 0 !important;
            font-size: 0 !important;
            line-height: 0 !important;
        }
    }
    </style>

    {{-- ===== SEGURIDAD DE SESIÓN POR PESTAÑA ===== --}}
    @auth
    <script>
    (function () {
        var KEY = 'uicm_tab_alive';

        @if(session('uicm_init'))
        sessionStorage.setItem(KEY, '1');
        @else
        if (!sessionStorage.getItem(KEY)) {
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
<body class="bg-uicm-gray font-sans antialiased text-gray-800"
      x-data="{
          sidebarOpen: false,
          sidebarCollapsed: localStorage.getItem('uicm_sidebar') === '1',
          modalCambiarPassword: {{ $errors->has('password') ? 'true' : 'false' }},
          userMenuOpen: false,
          toggleCollapse() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('uicm_sidebar', this.sidebarCollapsed ? '1' : '0');
          }
      }">

    {{-- ===== NAVBAR ===== --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-md" id="main-navbar">
        <div class="px-4 lg:px-6">
            <div class="relative flex items-center justify-between h-16">

                {{-- Lado izquierdo: logo + hamburguesa escritorio al extremo derecho --}}
                <div class="flex items-center gap-3 md:gap-0 md:justify-between md:w-64 md:px-4">

                    @auth
                    {{-- Hamburguesa móvil --}}
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

                    @auth
                    {{-- Hamburguesa escritorio: extremo derecho del contenedor del logo --}}
                    <button @click="toggleCollapse()"
                            class="hidden md:inline-flex items-center justify-center w-9 h-9 rounded-lg
                                   text-white transition-colors duration-150"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
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
                        'profesor'        => 'PROFESOR',
                        default           => strtoupper(auth()->user()->rol),
                    };
                    $norm = function (?string $t): string {
                        if (!$t) return '';
                        $t = normalizer_normalize($t, Normalizer::FORM_D);
                        $t = preg_replace('/\p{Mn}/u', '', $t);
                        return strtoupper($t);
                    };
                    if (auth()->user()->rol === 'alumno' && auth()->user()->alumno) {
                        $primerNombre    = explode(' ', trim(auth()->user()->alumno->nombre ?? ''))[0];
                        $apellidoPaterno = auth()->user()->alumno->apellido_paterno ?? '';
                    } else {
                        $primerNombre    = explode(' ', trim(auth()->user()->name ?? ''))[0];
                        $apellidoPaterno = auth()->user()->apellido_paterno ?? '';
                    }
                    $nombreMostrado = trim($norm($primerNombre) . ' ' . $norm($apellidoPaterno));
                    // Foto de perfil: primero la del usuario, luego la del aspirante (solo alumnos)
                    $fotoPerfil = auth()->user()->foto
                        ?: (auth()->user()->alumno?->aspirante?->foto_url ?? null);
                @endphp
                <span class="hidden md:block absolute left-64 pl-6 text-sm font-medium text-gray-600 whitespace-nowrap">
                    <span class="font-bold" style="color: #0F4229;">{{ $rolLabel }}</span>
                    <span class="text-gray-400 mx-1">—</span>
                    {{ $nombreMostrado }}
                </span>
                @endauth

                {{-- Lado derecho: dropdown de usuario --}}
                @auth
                <div class="relative">

                    {{-- Trigger: foto/iniciales + nombre + chevron --}}
                    <button @click="userMenuOpen = !userMenuOpen"
                            @keydown.escape.window="userMenuOpen = false"
                            class="flex items-center gap-2 px-2 py-1.5 rounded-xl transition-colors duration-150 hover:bg-gray-100">
                        <div class="w-8 h-8 rounded-full flex-shrink-0 overflow-hidden border-2"
                             style="border-color: #0F4229;">
                            @if($fotoPerfil)
                                <img src="{{ route('admin.archivo', ['path' => $fotoPerfil]) }}"
                                     alt="Foto de perfil"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-white text-xs font-extrabold"
                                     style="background-color: #0F4229;">
                                    {{ strtoupper(substr($primerNombre, 0, 1)) }}{{ $apellidoPaterno ? strtoupper(substr($apellidoPaterno, 0, 1)) : '' }}
                                </div>
                            @endif
                        </div>
                        <span class="hidden sm:block md:hidden text-sm font-medium text-gray-700 max-w-[130px] truncate">
                            {{ $nombreMostrado }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-150 flex-shrink-0"
                             :class="userMenuOpen && 'rotate-180'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Panel desplegable --}}
                    <div x-show="userMenuOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click.outside="userMenuOpen = false"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50"
                         style="display:none;">

                        {{-- Encabezado: foto + rol + nombre --}}
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex-shrink-0 overflow-hidden border-2"
                                 style="border-color: #0F4229;">
                                @if($fotoPerfil)
                                    <img src="{{ route('admin.archivo', ['path' => $fotoPerfil]) }}"
                                         alt="Foto de perfil"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white text-sm font-extrabold"
                                         style="background-color: #0F4229;">
                                        {{ strtoupper(substr($primerNombre, 0, 1)) }}{{ $apellidoPaterno ? strtoupper(substr($apellidoPaterno, 0, 1)) : '' }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-extrabold truncate" style="color: #0F4229;">{{ $rolLabel }}</p>
                                <p class="text-sm font-medium text-gray-700 truncate mt-0.5">{{ $nombreMostrado }}</p>
                            </div>
                        </div>

                        {{-- Cambiar contraseña --}}
                        <button type="button"
                                @click="userMenuOpen = false; $nextTick(() => { modalCambiarPassword = true })"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700
                                       hover:bg-gray-50 transition-colors duration-150 text-left">
                            <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                         a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Cambiar contraseña
                        </button>

                        <div class="border-t border-gray-100 mx-3"></div>

                        {{-- Cerrar sesión --}}
                        <form method="POST" action="{{ route('logout') }}"
                              onsubmit="localStorage.setItem('uicm_logout', Date.now()); sessionStorage.removeItem('uicm_tab_alive');">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600
                                           hover:bg-red-50 transition-colors duration-150 text-left">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7
                                             a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Cerrar sesión
                            </button>
                        </form>

                    </div>
                </div>
                @else
                {{-- Navbar público --}}
                @unless(request()->routeIs('login'))
                <button id="mobile-menu-btn"
                        class="md:hidden inline-flex items-center justify-center w-9 h-9 rounded-lg
                               text-white transition-colors duration-150 focus:outline-none"
                        style="background-color: #0F4229;"
                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                        onmouseout="this.style.backgroundColor='#0F4229'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="hamburger-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-uicm-green hover:text-green-800 transition-colors">Inicio</a>
                    <a href="{{ route('aspirantes.seguimiento') }}" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Consultar estatus</a>
                    <a href="{{ request()->routeIs('oferta-educativa') ? '#contacto' : route('home').'#contacto' }}" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Contáctanos</a>
                    <a href="{{ route('aspirantes.registro') }}" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Inscripción</a>
                    <a href="{{ route('oferta-educativa') }}" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Oferta educativa</a>
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
                <a href="{{ route('aspirantes.seguimiento') }}" class="text-base font-medium text-gray-600 hover:text-uicm-green">Consultar estatus</a>
                <a href="{{ request()->routeIs('oferta-educativa') ? '#contacto' : route('home').'#contacto' }}" class="text-base font-medium text-gray-600 hover:text-uicm-green">Contáctanos</a>
                <a href="{{ route('aspirantes.registro') }}" class="text-base font-medium text-gray-600 hover:text-uicm-green">Inscripción</a>
                <a href="{{ route('oferta-educativa') }}" class="text-base font-medium text-gray-600 hover:text-uicm-green">Oferta educativa</a>
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

    <aside class="sidebar-anim fixed left-0 top-16 bottom-0 z-40 w-64 overflow-y-auto flex flex-col"
           :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0', sidebarCollapsed ? 'sidebar-collapsed-md' : '']"
           style="background-color: #0F4229;">

        {{-- Navegación --}}
        <nav class="flex-1 px-3 py-3">

            @php $rol = auth()->user()->rol; @endphp

            {{-- ══ INICIO (todos los roles) ══ --}}
            @php
                $inicioRoute = match($rol) {
                    'alumno' => 'alumno.dashboard',
                    default  => 'dashboard',
                };
                $inicioActivo = request()->routeIs($inicioRoute);
            @endphp
            <a href="{{ route($inicioRoute) }}"
               @click="sidebarOpen = false"
               data-tooltip="Inicio"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ $inicioActivo ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="nav-link-text">Inicio</span>
            </a>

            {{-- ══ PORTAL DEL PROFESOR ══ --}}
            @if($rol === 'profesor')
            <div class="nav-section-label pt-2 pb-1 px-2 mt-1 border-t border-white/10">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Portal del profesor</p>
            </div>

            @php
                $profesorActual = \App\Models\Profesor::where('user_id', auth()->id())->first();
                $aclaracionesProfesorPendientes = $profesorActual
                    ? \App\Models\AclaracionCalificacion::where('profesor_id', $profesorActual->id)->where('estado', 'pendiente')->count()
                    : 0;
            @endphp
            <a href="{{ route('profesor.aclaraciones.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Aclaraciones"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('profesor.aclaraciones.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14
                             a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4v-4z"/>
                </svg>
                <span class="flex-1 nav-link-text">Aclaraciones</span>
                @if($aclaracionesProfesorPendientes > 0)
                    <span class="nav-badge inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                          style="background-color: #dc2626; color: #fff;">
                        {{ $aclaracionesProfesorPendientes }}
                    </span>
                @endif
            </a>

            <a href="{{ route('profesor.alumnos.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Alumnos"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('profesor.alumnos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="nav-link-text">Alumnos</span>
            </a>

            <a href="{{ route('profesor.calificaciones.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Calificaciones"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('profesor.calificaciones.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                             m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="nav-link-text">Calificaciones</span>
            </a>

            <a href="{{ route('profesor.grupos.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Grupos"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('profesor.grupos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="nav-link-text">Grupos</span>
            </a>

            <a href="{{ route('profesor.horario.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Horario"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('profesor.horario.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5
                             a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="nav-link-text">Horario</span>
            </a>
            @endif

            {{-- ══ ADMIN ══ --}}
            @if($rol === 'admin')
            <div class="nav-section-label pt-2 pb-1 px-2 mt-1 border-t border-white/10">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Administración</p>
            </div>

            <a href="{{ route('admin.apis.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="APIs"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.apis.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 20l4-16m4 4l4 4-4 4M6 8l-4 4 4 4"/>
                </svg>
                <span class="nav-link-text">APIs</span>
            </a>

            @php
                $pendientesAdmins = \App\Models\SolicitudContrasena::where('estado', 'pendiente')
                    ->whereHas('user', fn($u) => $u->whereNotIn('rol', ['alumno', 'profesor']))
                    ->count();
            @endphp
            <a href="{{ route('admin.solicitudes-contrasena.index', ['tipo' => 'administrativos']) }}"
               @click="sidebarOpen = false"
               data-tooltip="Contraseñas"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.solicitudes-contrasena.*') && request()->query('tipo') === 'administrativos' ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                             a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                <span class="flex-1 nav-link-text">Contraseñas</span>
                @if($pendientesAdmins > 0)
                    <span class="nav-badge inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                          style="background-color: #dc2626; color: #fff;">
                        {{ $pendientesAdmins }}
                    </span>
                @endif
            </a>

            @php
                $mensajesPendientes = \App\Models\Contacto::where('atendido', false)->count();
            @endphp
            <a href="{{ route('admin.contactos.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Mensajes de contacto"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.contactos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="flex-1 nav-link-text">Mensajes de contacto</span>
                @if($mensajesPendientes > 0)
                    <span class="nav-badge inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                          style="background-color: #dc2626; color: #fff;">
                        {{ $mensajesPendientes }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.pagina-principal.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Página principal"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.pagina-principal.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="nav-link-text">Página principal</span>
            </a>

            <a href="{{ route('admin.usuarios.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Usuarios del sistema"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.usuarios.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="nav-link-text">Usuarios del sistema</span>
            </a>
            @endif

            {{-- ══ COORDINACIÓN ACADÉMICA ══ --}}
            @if($rol === 'coordinacion' || $rol === 'admin')
            <div class="nav-section-label mt-4 border-t border-white/10 pt-3 pb-1 px-2">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Coordinación Académica</p>
            </div>

            <a href="{{ route('admin.aclaraciones.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Aclaraciones"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.aclaraciones.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8
                             a2 2 0 01-2 2h-5l-4 4v-4z"/>
                </svg>
                <span class="nav-link-text">Aclaraciones</span>
            </a>

            <a href="{{ route('admin.alta-masiva-alumnos.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Alta masiva de alumnos"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.alta-masiva-alumnos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3 3-3m-3-7v9"/>
                </svg>
                <span class="nav-link-text">Alta masiva de alumnos</span>
            </a>

            <a href="{{ route('admin.calificaciones.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Calificaciones"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.calificaciones.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="nav-link-text">Calificaciones</span>
            </a>

            <a href="{{ route('admin.carga-academica.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Carga académica"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.carga-academica.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5
                             a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="nav-link-text">Carga académica</span>
            </a>

            @php
                $pendientesProfesores = \App\Models\SolicitudContrasena::where('estado', 'pendiente')
                    ->whereHas('user', fn($u) => $u->where('rol', 'profesor'))
                    ->count();
            @endphp
            <a href="{{ route('admin.contrasenas-profesores.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Contraseñas"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.contrasenas-profesores.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                             a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                <span class="flex-1 nav-link-text">Contraseñas</span>
                @if($pendientesProfesores > 0)
                    <span class="nav-badge inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                          style="background-color: #dc2626; color: #fff;">
                        {{ $pendientesProfesores }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.periodos.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Cuatrimestres"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.periodos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="nav-link-text">Cuatrimestres</span>
            </a>

            <a href="{{ route('admin.grupos.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Grupos"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.grupos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="nav-link-text">Grupos</span>
            </a>

            <a href="{{ route('admin.materias.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Materias"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.materias.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                             C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                             C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                             C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="nav-link-text">Materias</span>
            </a>

            <a href="{{ route('admin.profesores.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Profesores"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.profesores.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="nav-link-text">Profesores</span>
            </a>

            <a href="{{ route('admin.programas.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Programas"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.programas.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="nav-link-text">Programas</span>
            </a>
            @endif

            {{-- ══ CONTROL ESCOLAR ══ --}}
            @if($rol === 'control_escolar' || $rol === 'admin')
            <div class="nav-section-label mt-4 border-t border-white/10 pt-3 pb-1 px-2">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Control Escolar</p>
            </div>

            <a href="{{ route('admin.alumnos.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Alumnos"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.alumnos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                             M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                             m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="nav-link-text">Alumnos</span>
            </a>

            @php
                $pendientesAspirantes = \App\Models\Aspirante::where('estado', 'pendiente')->count();
            @endphp
            <a href="{{ route('admin.aspirantes.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Aspirantes"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.aspirantes.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                             a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="flex-1 nav-link-text">Aspirantes</span>
                @if($pendientesAspirantes > 0)
                    <span class="nav-badge inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                          style="background-color: #dc2626; color: #fff;">
                        {{ $pendientesAspirantes }}
                    </span>
                @endif
            </a>

            @php
                $pendientesAlumnos = \App\Models\SolicitudContrasena::where('estado', 'pendiente')
                    ->whereHas('user', fn($u) => $u->where('rol', 'alumno'))
                    ->count();
            @endphp
            <a href="{{ route('admin.solicitudes-contrasena.index', ['tipo' => 'alumnos']) }}"
               @click="sidebarOpen = false"
               data-tooltip="Contraseñas"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.solicitudes-contrasena.*') && request()->query('tipo', 'alumnos') === 'alumnos' ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                             a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                <span class="flex-1 nav-link-text">Contraseñas</span>
                @if($pendientesAlumnos > 0)
                    <span class="nav-badge inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                          style="background-color: #dc2626; color: #fff;">
                        {{ $pendientesAlumnos }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.expedientes.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Expedientes"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.expedientes.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                             a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="nav-link-text">Expedientes</span>
            </a>

            @php
                $pendientesInscripciones = \App\Models\Aspirante::where('estado', 'aprobado')
                    ->whereHas('pagos', fn($q) => $q->where('estado', 'aprobado'))
                    ->whereHas('alumno', fn($q) => $q->whereNull('user_id'))
                    ->count();
            @endphp
            <a href="{{ route('admin.inscripciones.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Inscripciones"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.inscripciones.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5
                             m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4z"/>
                </svg>
                <span class="flex-1 nav-link-text">Inscripciones</span>
                @if($pendientesInscripciones > 0)
                    <span class="nav-badge inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                          style="background-color: #dc2626; color: #fff;">
                        {{ $pendientesInscripciones }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.reinscripciones.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Reinscripciones"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.reinscripciones.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span class="nav-link-text">Reinscripciones</span>
            </a>
            @endif

            {{-- ══ FINANZAS ══ --}}
            @if($rol === 'finanzas' || $rol === 'admin')
            <div class="nav-section-label mt-4 border-t border-white/10 pt-3 pb-1 px-2">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Finanzas</p>
            </div>

            <a href="{{ route('finanzas.alumnos') }}"
               @click="sidebarOpen = false"
               data-tooltip="Alumnos al corriente"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('finanzas.alumnos') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-3.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/>
                </svg>
                <span class="nav-link-text">Alumnos al corriente</span>
            </a>

            <a href="{{ route('finanzas.tarifas.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Conceptos de pago"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('finanzas.tarifas.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="nav-link-text">Conceptos de pago</span>
            </a>

            <a href="{{ route('finanzas.estadisticas') }}"
               @click="sidebarOpen = false"
               data-tooltip="Estadísticas"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('finanzas.estadisticas') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm6 0V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2zm6 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"/>
                </svg>
                <span class="nav-link-text">Estadísticas</span>
            </a>

            @php
                $pagosPendientes = \App\Models\Pago::conIntentoDePago()->where('estado', 'pendiente')->count();
            @endphp
            <a href="{{ route('finanzas.pagos.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Validación de pagos"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('finanzas.pagos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10
                             a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="flex-1 nav-link-text">Validación de pagos</span>
                @if($pagosPendientes > 0)
                    <span class="nav-badge inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
                          style="background-color: #dc2626; color: #fff;">
                        {{ $pagosPendientes }}
                    </span>
                @endif
            </a>
            @endif

            {{-- ══ ALUMNO ══ --}}
            @if($rol === 'alumno')
            <div class="nav-section-label pt-2 pb-1 px-2 mt-1 border-t border-white/10">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-70" style="color: #D4AF37;">Portal del alumno</p>
            </div>

            <a href="{{ route('alumno.documentos.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Documentos"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('alumno.documentos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293
                             l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="nav-link-text">Documentos</span>
            </a>

            <a href="{{ route('alumno.finanzas.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Finanzas"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('alumno.finanzas.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6
                             a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
                <span class="nav-link-text">Finanzas</span>
            </a>

            <a href="{{ route('alumno.kardex') }}"
               @click="sidebarOpen = false"
               data-tooltip="Historial académico"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('alumno.kardex*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                             m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="nav-link-text">Historial académico</span>
            </a>

            <a href="{{ route('alumno.horario.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Horario"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('alumno.horario.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5
                             a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="nav-link-text">Horario</span>
            </a>

            <a href="{{ route('alumno.materias.index') }}"
               @click="sidebarOpen = false"
               data-tooltip="Materias"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-150
                      {{ request()->routeIs('alumno.materias.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13
                             C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13
                             C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13
                             C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="nav-link-text">Materias</span>
            </a>
            @endif

        </nav>


    </aside>
    @endauth

    {{-- ===== MODAL: CAMBIAR CONTRASEÑA (todos los roles) ===== --}}
    @auth
    @php
        $cambiarPasswordRoute = match(auth()->user()->rol) {
            'alumno'  => route('alumno.cambiar-password'),
            'profesor' => route('profesor.cambiar-password'),
            default   => route('perfil.cambiar-password'),
        };
    @endphp
    @if(true)
    <div x-show="modalCambiarPassword"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display: none;">

        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
             @click.outside="modalCambiarPassword = false">

            <div class="h-1.5 w-full" style="background-color: #EFAD5A;"></div>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #EFAD5A;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                 a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <h2 class="text-sm font-bold text-gray-800">Cambiar contraseña</h2>
                </div>
                <button type="button" @click="modalCambiarPassword = false"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400
                               hover:text-gray-600 hover:bg-gray-100 transition-colors duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ $cambiarPasswordRoute }}" class="px-6 py-5 space-y-4"
                  x-data="{ showPwd: false }">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Nueva contraseña <span class="normal-case font-normal text-gray-400">(mín. 8 caracteres)</span>
                    </label>
                    <input :type="showPwd ? 'text' : 'password'" name="password" required
                           class="w-full px-4 py-2.5 text-sm border rounded-xl bg-white focus:outline-none
                                  @error('password') border-red-400 @else border-gray-300 @enderror"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                           onblur="this.style.borderColor=''; this.style.boxShadow=''">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Confirmar contraseña
                    </label>
                    <input :type="showPwd ? 'text' : 'password'" name="password_confirmation" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.15)'"
                           onblur="this.style.borderColor=''; this.style.boxShadow=''">
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button type="button" @click="showPwd = !showPwd"
                            class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 transition-colors select-none">
                        <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                        </svg>
                        <span x-text="showPwd ? 'Ocultar contraseñas' : 'Mostrar contraseñas'"></span>
                    </button>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="modalCambiarPassword = false"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold border-2 transition-colors duration-150"
                            style="border-color: #0F4229; color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#f0f9f4'"
                            onmouseout="this.style.backgroundColor='transparent'">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold text-white transition-colors duration-200"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endauth

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    @auth
    <div class="main-anim main-auth flex flex-col" style="min-height:calc(100vh - 4rem)" :class="sidebarCollapsed ? 'sidebar-collapsed-md' : ''">
    @else
    <div class="flex flex-col" style="min-height:calc(100vh - 4rem)">
    @endauth
        <main class="grow">
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

    <script>
        function formatTelefono(el) {
            el.value = el.value.replace(/\D/g, '').substring(0, 10);
        }
        function formatPeriodo(el) {
            var v = el.value.replace(/\D/g, '').substring(0, 5);
            el.value = v.length > 4 ? v.slice(0, 4) + '-' + v.slice(4) : v;
        }
    </script>

    @stack('scripts')

    {{-- Tooltips del sidebar colapsado --}}
    @auth
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var tip = document.createElement('div');
        tip.style.cssText = 'position:fixed;background:rgba(15,66,41,0.95);color:#fff;padding:5px 10px;border-radius:6px;font-size:12px;white-space:nowrap;z-index:9999;pointer-events:none;opacity:0;transition:opacity 0.15s ease-in-out;display:none;';
        document.body.appendChild(tip);

        var hideTimer = null;

        document.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('mouseenter', function () {
                if (!document.querySelector('aside.sidebar-collapsed-md')) return;
                var text = link.getAttribute('data-tooltip');
                if (!text) return;
                if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }
                var r = link.getBoundingClientRect();
                tip.textContent = text;
                tip.style.display = 'block';
                tip.style.left = (r.right + 10) + 'px';
                tip.style.top  = (r.top + r.height / 2) + 'px';
                tip.style.transform = 'translateY(-50%)';
                tip.style.opacity = '1';
            });
            link.addEventListener('mouseleave', function () {
                tip.style.opacity = '0';
                hideTimer = setTimeout(function () { tip.style.display = 'none'; hideTimer = null; }, 150);
            });
        });
    });
    </script>
    @endauth

    {{-- ===== TOAST NOTIFICATIONS ===== --}}
    @php
        $toasts = [];
        if (session('success'))          $toasts[] = ['ok', session('success')];
        if (session('password_success')) $toasts[] = ['ok', session('password_success')];
        if (session('success_oferta'))   $toasts[] = ['ok', session('success_oferta')];
        if (session('success_carrusel')) $toasts[] = ['ok', session('success_carrusel')];
        if (session('success_contacto')) $toasts[] = ['ok', session('success_contacto')];
        if (session('contacto_enviado')) $toasts[] = ['ok', '¡Mensaje enviado! Un asesor se pondrá en contacto a la brevedad.'];
        if (session('error'))            $toasts[] = ['err', session('error')];
        if (session('notif_error'))      $toasts[] = ['err', session('notif_error')];
        foreach (['control_escolar', 'finanzas', 'coordinacion', 'admin'] as $_nr) {
            if (session('notif_ok_'.$_nr))      $toasts[] = ['ok',   session('notif_ok_'.$_nr)];
            if (session('notif_warning_'.$_nr)) $toasts[] = ['warn', session('notif_warning_'.$_nr)];
        }
        if ($errors->any()) {
            $allErrs = $errors->all();
            $shown   = min(count($allErrs), 3);
            for ($i = 0; $i < $shown; $i++) $toasts[] = ['err', $allErrs[$i]];
            if (count($allErrs) > 3) {
                $resto = array_slice($allErrs, 3);
                $listaResto = implode("\n", array_map(fn ($e) => "• {$e}", $resto));
                $toasts[] = ['err', 'Y ' . count($resto) . " error(es) más:\n" . $listaResto];
            }
        }
    @endphp

    @if(count($toasts) > 0)
    <div id="toast-container" aria-live="polite"
         style="position:fixed; top:1.25rem; right:1.25rem; z-index:9999; display:flex; flex-direction:column; gap:0.625rem; width:20rem; pointer-events:none;">
        @foreach($toasts as [$type, $msg])
        @php
            $tColor = match($type) { 'ok' => '#0F4229', 'warn' => '#EFAD5A', default => '#ef4444' };
            $tIcon  = match($type) {
                'ok'   => 'M5 13l4 4L19 7',
                'warn' => 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
                default => 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            };
            $tText  = match($type) { 'ok' => 'text-green-800', 'warn' => 'text-amber-800', default => 'text-red-700' };
        @endphp
        <div class="toast-item pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-lg bg-white"
             style="border-left: 4px solid {{ $tColor }};"
             data-delay="{{ $type === 'err' ? 7000 : 5000 }}">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 style="color: {{ $tColor }}; flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $tIcon }}"/>
            </svg>
            <p class="text-sm font-semibold flex-1 leading-snug {{ $tText }}"
               style="flex:1; white-space: pre-line;">{{ trim($msg) }}</p>
            <button type="button" class="toast-close text-gray-400 hover:text-gray-600 transition-colors"
                    style="flex-shrink:0; padding:2px; margin-left:4px;"
                    aria-label="Cerrar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ===== AUTO-DISMISS DE TOASTS ===== --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function dismissToast(el) {
            el.classList.add('toast-out');
            setTimeout(function () { el.remove(); }, 300);
        }
        document.querySelectorAll('.toast-item').forEach(function (el) {
            var delay = parseInt(el.dataset.delay) || 5000;
            var timer = setTimeout(function () { dismissToast(el); }, delay);
            var btn = el.querySelector('.toast-close');
            if (btn) btn.addEventListener('click', function () { clearTimeout(timer); dismissToast(el); });
        });
    });
    </script>

    {{-- ===== SINCRONIZACIÓN DE CIERRE DE SESIÓN ENTRE PESTAÑAS ===== --}}
    @auth
    <script>
    (function () {
        var KEY = 'uicm_tab_alive';

        sessionStorage.setItem(KEY, '1');

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
    sessionStorage.removeItem('uicm_tab_alive');
    </script>
    @endguest
</body>
</html>
