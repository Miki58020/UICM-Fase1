<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Bienvenida --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            {{-- Accesos rápidos del panel --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Portal del alumno --}}
                <a href="{{ route('alumno.dashboard') }}"
                   class="flex items-center gap-4 bg-white rounded-xl shadow-sm px-6 py-5
                          border border-transparent hover:border-green-200 hover:shadow-md
                          transition-all duration-200 group">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center"
                         style="background-color: #f0f9f4;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055
                                     a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold group-hover:underline" style="color: #0F4229;">
                            Portal del alumno
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Materias, horarios e historial de pagos</p>
                    </div>
                </a>

                {{-- Validar aspirantes --}}
                <a href="{{ route('admin.aspirantes.index') }}"
                   class="flex items-center gap-4 bg-white rounded-xl shadow-sm px-6 py-5
                          border border-transparent hover:border-green-200 hover:shadow-md
                          transition-all duration-200 group">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center"
                         style="background-color: #f0f9f4;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold group-hover:underline" style="color: #0F4229;">
                            Validar aspirantes
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Revisión y aprobación de expedientes</p>
                    </div>
                </a>

                {{-- Generar matrículas --}}
                <a href="{{ route('admin.inscripciones.index') }}"
                   class="flex items-center gap-4 bg-white rounded-xl shadow-sm px-6 py-5
                          border border-transparent hover:border-green-200 hover:shadow-md
                          transition-all duration-200 group">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center"
                         style="background-color: #f0f9f4;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                                     a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964
                                     A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold group-hover:underline" style="color: #0F4229;">
                            Generar matrículas
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Inscripción y creación de cuentas</p>
                    </div>
                </a>

                {{-- Gestión de profesores --}}
                <a href="{{ route('admin.profesores.index') }}"
                   class="flex items-center gap-4 bg-white rounded-xl shadow-sm px-6 py-5
                          border border-transparent hover:border-green-200 hover:shadow-md
                          transition-all duration-200 group">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center"
                         style="background-color: #f0f9f4;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                                     M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                                     m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold group-hover:underline" style="color: #0F4229;">
                            Gestión de profesores
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Planta docente y asignaciones</p>
                    </div>
                </a>

                {{-- Carga académica --}}
                <a href="{{ route('admin.carga-academica.index') }}"
                   class="flex items-center gap-4 bg-white rounded-xl shadow-sm px-6 py-5
                          border border-transparent hover:border-green-200 hover:shadow-md
                          transition-all duration-200 group">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center"
                         style="background-color: #f0f9f4;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                     M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                                     m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold group-hover:underline" style="color: #0F4229;">
                            Carga académica
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Generación de carga por grupo y ciclo</p>
                    </div>
                </a>

                {{-- Validar pagos --}}
                <a href="{{ route('finanzas.pagos.index') }}"
                   class="flex items-center gap-4 bg-white rounded-xl shadow-sm px-6 py-5
                          border border-transparent hover:border-green-200 hover:shadow-md
                          transition-all duration-200 group">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center"
                         style="background-color: #f0f9f4;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: #0F4229;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10
                                     a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold group-hover:underline" style="color: #0F4229;">
                            Validar pagos
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Revisión de comprobantes de inscripción</p>
                    </div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>
