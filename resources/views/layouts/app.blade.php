<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Universidad Internacional Cuba México')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-uicm-gray font-sans antialiased text-gray-800" x-data="{ sidebarOpen: false }">

    {{-- ===== NAVBAR ===== --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-md" id="main-navbar">
        <div class="px-4 lg:px-6">
            <div class="flex items-center justify-between h-16">

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

                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo UICM" class="h-10 w-auto">
                    </a>
                </div>

                {{-- Lado derecho --}}
                @auth
                <div class="flex items-center gap-3">
                    <span class="hidden sm:block text-xs text-gray-400 font-medium">
                        {{ auth()->user()->email }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
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
                <a href="{{ route('home') }}" class="text-sm font-medium text-uicm-green">Inicio</a>
                <a href="{{ route('home') }}#oferta" class="text-sm font-medium text-gray-600 hover:text-uicm-green">Oferta educativa</a>
                <a href="{{ route('aspirantes.registro') }}" class="text-sm font-medium text-gray-600 hover:text-uicm-green">Inscripción</a>
                <a href="{{ route('aspirantes.seguimiento') }}" class="text-sm font-medium text-gray-600 hover:text-uicm-green">Consultar estatus</a>
                <a href="{{ route('home') }}#contacto" class="text-sm font-medium text-gray-600 hover:text-uicm-green">Contáctanos</a>
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
        <div class="px-4 py-5 border-b border-white/10">
            <p class="text-xs text-green-300 uppercase tracking-widest font-semibold mb-0.5">Bienvenido</p>
            <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5">

            {{-- Panel principal --}}
            <a href="{{ route('dashboard') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3
                             m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Panel principal
            </a>

            {{-- ── Control Escolar ── --}}
            <div class="pt-4 pb-1 px-3">
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #D4AF37;">Control Escolar</p>
            </div>

            <a href="{{ route('admin.aspirantes.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
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
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.inscripciones.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5
                             m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4z"/>
                </svg>
                Inscripciones
            </a>

            {{-- ── Finanzas ── --}}
            <div class="pt-4 pb-1 px-3">
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #D4AF37;">Finanzas</p>
            </div>

            <a href="{{ route('finanzas.pagos.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('finanzas.pagos.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10
                             a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Validación de pagos
            </a>

            {{-- ── Coordinación Académica ── --}}
            <div class="pt-4 pb-1 px-3">
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #D4AF37;">Coordinación Académica</p>
            </div>

            <a href="{{ route('admin.materias.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
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
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.profesores.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profesores
            </a>

            <a href="{{ route('admin.carga-academica.index') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.carga-academica.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5
                             a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Carga académica
            </a>

            {{-- ── Portal del alumno ── --}}
            <div class="pt-4 pb-1 px-3">
                <p class="text-xs font-bold uppercase tracking-widest" style="color: #D4AF37;">Portal del alumno</p>
            </div>

            <a href="{{ route('alumno.dashboard') }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('alumno.*') ? 'bg-white/20 text-white' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012
                             20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                Mi portal
            </a>

        </nav>

        {{-- Versión / pie del sidebar --}}
        <div class="px-4 py-3 border-t border-white/10">
            <p class="text-xs text-green-400 text-center">UICM · Sistema de Gestión</p>
        </div>

    </aside>
    @endauth

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    <div class="{{ auth()->check() ? 'md:ml-64' : '' }}">
        <main>
            @yield('content')
        </main>

        {{-- ===== FOOTER ===== --}}
        <footer class="text-white text-center py-5" style="background-color: #0F4229;">
            <div class="container mx-auto px-8 lg:px-24">
                <div class="mb-2">
                    <span class="font-bold text-lg tracking-wide">UICM</span>
                </div>
                <p class="text-green-200 text-sm mb-1">Universidad Internacional Cuba México</p>
                <p class="text-green-300 text-xs">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
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
</body>
</html>
