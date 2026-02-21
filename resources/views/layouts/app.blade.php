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
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo UICM" class="h-10 w-auto">
                </a>

                {{-- Botón hamburguesa (móvil) --}}
                <button id="mobile-menu-btn"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-uicm-green hover:bg-gray-100 focus:outline-none"
                        aria-controls="nav-menu" aria-expanded="false">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="hamburger-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Links escritorio --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-uicm-green hover:text-green-800 transition-colors">Inicio</a>
                    <a href="#oferta" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Oferta educativa</a>
                    <a href="#contacto" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Contáctanos</a>
                    <a href="{{ route('aspirantes.seguimiento') }}" class="text-sm font-medium text-gray-600 hover:text-uicm-green transition-colors">Consultar estatus</a>
                    <a href="{{ route('login') }}"
                       class="ml-2 px-4 py-2 rounded-md text-sm font-semibold text-white transition-colors duration-200"
                       style="background-color: #0F4229;"
                       onmouseover="this.style.backgroundColor='#0a2f1c'"
                       onmouseout="this.style.backgroundColor='#0F4229'">
                        Iniciar sesión
                    </a>
                </div>
            </div>
        </div>

        {{-- Menú móvil desplegable --}}
        <div id="nav-menu" class="hidden md:hidden bg-white border-t border-gray-100 px-4 pb-4">
            <div class="flex flex-col gap-3 pt-3">
                <a href="{{ route('home') }}" class="text-sm font-medium text-uicm-green">Inicio</a>
                <a href="#oferta" class="text-sm font-medium text-gray-600 hover:text-uicm-green">Oferta educativa</a>
                <a href="#contacto" class="text-sm font-medium text-gray-600 hover:text-uicm-green">Contáctanos</a>
                <a href="{{ route('aspirantes.seguimiento') }}" class="text-sm font-medium text-gray-600 hover:text-uicm-green">Consultar estatus</a>
                <a href="{{ route('login') }}"
                   class="w-full text-center px-4 py-2 rounded-md text-sm font-semibold text-white"
                   style="background-color: #0F4229;">
                    Iniciar sesión
                </a>
            </div>
        </div>
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
