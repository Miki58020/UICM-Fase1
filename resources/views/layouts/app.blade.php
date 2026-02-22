<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Universidad Internacional Cuba México')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-uicm-gray font-sans antialiased text-gray-800">

    {{-- ===== NAVBAR ===== --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-md" id="main-navbar">
        <div class="container mx-auto px-8 lg:px-24">
            <div class="flex items-center justify-between h-16">

                {{-- Marca --}}
                <a href="{{ auth()->check() ? route('dashboard') : route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo UICM" class="h-10 w-auto">
                </a>

                @auth
                {{-- ── Navbar administrador ── --}}
                <div class="flex items-center gap-4">
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
                            Cerrar sesión
                        </button>
                    </form>
                </div>
                @else
                {{-- ── Navbar público (oculto en login) ── --}}
                @unless(request()->routeIs('login'))
                <button id="mobile-menu-btn"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-uicm-green hover:bg-gray-100 focus:outline-none"
                        aria-controls="nav-menu" aria-expanded="false">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="hamburger-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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

        {{-- Menú móvil — solo para invitados fuera del login --}}
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

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
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

    {{-- Script menú móvil --}}
    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('nav-menu');
        const hamburger = document.getElementById('hamburger-icon');
        const closeIcon = document.getElementById('close-icon');

        btn.addEventListener('click', () => {
            const isOpen = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            hamburger.classList.toggle('hidden', !isOpen ? true : false);
            closeIcon.classList.toggle('hidden', !isOpen ? false : true);
        });

        // Cambiar sombra del navbar al hacer scroll
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('main-navbar');
            if (window.scrollY > 10) {
                navbar.classList.add('shadow-lg');
            } else {
                navbar.classList.remove('shadow-lg');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
