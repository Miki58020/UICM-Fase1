@extends('layouts.app')

@section('title', 'Usuarios del sistema | UICM')

@php
$rolesLabels = [
    'admin'           => ['label' => 'Administrador',         'color' => '#0F4229'],
    'control_escolar' => ['label' => 'Control Escolar',       'color' => '#EFAD5A'],
    'finanzas'        => ['label' => 'Finanzas',              'color' => '#D4AF37'],
    'coordinacion'    => ['label' => 'Coordinación Académica','color' => '#1F5FBF'],
    'alumno'          => ['label' => 'Alumno',                'color' => '#9ca3af'],
];
@endphp

@section('content')

<section
    x-data="{
        showModal: {{ $errors->any() ? 'true' : 'false' }},
        showDeleteModal: false,
        editando: {{ $errors->any() && old('_method') === 'PATCH' && old('edit_usuario_id') ? '{ id: ' . (int) old('edit_usuario_id') . ' }' : 'null' }},
        deletingId: null,
        deletingName: '',
        form: {
            name:             '{{ addslashes(old('name', '')) }}',
            apellido_paterno: '{{ addslashes(old('apellido_paterno', '')) }}',
            apellido_materno: '{{ addslashes(old('apellido_materno', '')) }}',
            email:            '{{ addslashes(old('email', '')) }}',
            password:         '',
            rol:              '{{ old('rol', '') }}'
        },
        busqueda: '',
        filtroRol: '',
        coincide(fila) {
            const texto = this.busqueda.toLowerCase();
            const nombre = fila.dataset.nombre.toLowerCase();
            const email  = fila.dataset.email.toLowerCase();
            const rol    = fila.dataset.rol;
            const pasaBusqueda = !texto || nombre.includes(texto) || email.includes(texto);
            const pasaFiltro   = !this.filtroRol || rol === this.filtroRol;
            return pasaBusqueda && pasaFiltro;
        },
        filtrar() {
            this.$nextTick(() => {
                const filas = this.$refs.tbody.querySelectorAll('tr[data-nombre]');
                let visibles = 0;
                filas.forEach(f => {
                    const mostrar = this.coincide(f);
                    f.style.display = mostrar ? '' : 'none';
                    if (mostrar) visibles++;
                });
                this.$refs.sinResultados.style.display = visibles === 0 ? '' : 'none';
                this.$refs.contadorVisible.textContent = visibles + ' registro' + (visibles !== 1 ? 's' : '');
            });
        },
        abrir() {
            this.editando = null;
            this.form = { name: '', apellido_paterno: '', apellido_materno: '', email: '', password: '', rol: '' };
            this.showModal = true;
        },
        abrirEditar(u) {
            this.editando = u;
            this.form = { name: u.name, apellido_paterno: u.apellido_paterno, apellido_materno: u.apellido_materno, email: u.email, password: '', rol: u.rol };
            this.showModal = true;
        },
        abrirEliminar(id, name) {
            this.deletingId = id;
            this.deletingName = name;
            this.showDeleteModal = true;
        }
    }"
    class="bg-uicm-gray min-h-screen py-12 px-4">

    <div class="container mx-auto px-4 lg:px-12 max-w-7xl">

        {{-- Encabezado --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                    Administración
                </p>
                <h1 class="text-2xl font-extrabold text-gray-900">Usuarios del sistema</h1>
                <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
            </div>
            <button type="button" @click="abrir()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold
                       text-white shadow-sm transition-colors duration-200 self-start sm:self-auto"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2e1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo usuario
            </button>
        </div>

        {{-- Contadores por rol (también filtran; sincronizados con el dropdown de abajo) --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-8">
            <button type="button" @click="filtroRol = ''; filtrar()"
                    :class="filtroRol === '' ? 'ring-2 ring-gray-400' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: #6B7280;">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total</p>
                <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $usuarios->count() }}</p>
            </button>
            @foreach(['admin' => 'Administradores', 'control_escolar' => 'Control Escolar', 'finanzas' => 'Finanzas', 'coordinacion' => 'Coordinación'] as $rol => $label)
            <button type="button" @click="filtroRol = '{{ $rol }}'; filtrar()"
                    :class="filtroRol === '{{ $rol }}' ? 'ring-2' : 'hover:shadow-md'"
                    class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4 text-left w-full transition-shadow duration-150"
                    style="border-color: {{ $rolesLabels[$rol]['color'] }};">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">{{ $label }}</p>
                <p class="text-2xl font-extrabold mt-1" style="color: {{ $rolesLabels[$rol]['color'] }};">
                    {{ $conteo[$rol] }}
                </p>
            </button>
            @endforeach
        </div>

        {{-- Búsqueda y filtro --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            {{-- Buscador --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text"
                       x-model="busqueda"
                       @input="filtrar()"
                       placeholder="Buscar por nombre o correo…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            </div>

            {{-- Filtro por rol --}}
            <div class="relative sm:w-56">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <select x-model="filtroRol"
                        @change="filtrar()"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                        onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <option value="">Todos los roles</option>
                    <option value="admin">Administrador</option>
                    <option value="control_escolar">Control Escolar</option>
                    <option value="finanzas">Finanzas</option>
                    <option value="coordinacion">Coordinación Académica</option>
                </select>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Listado de usuarios</h2>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green" x-ref="contadorVisible">{{ $usuarios->count() }} registros</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Correo</th>
                            <th class="px-6 py-3">Rol</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300" x-ref="tbody">
                        @forelse($usuarios as $u)
                        <tr class="hover:bg-gray-50 transition-colors duration-100
                                   {{ $u->id === auth()->id() ? 'bg-green-50/40' : '' }}"
                            data-nombre="{{ strtolower($u->nombre_completo) }}"
                            data-email="{{ strtolower($u->email) }}"
                            data-rol="{{ $u->rol }}">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full flex-shrink-0 overflow-hidden border-2"
                                         style="border-color: #0F4229;">
                                        @if($u->foto)
                                            <img src="{{ route('admin.archivo', ['path' => $u->foto]) }}"
                                                 alt="Foto de perfil"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-white text-xs font-bold"
                                                 style="background-color: {{ $rolesLabels[$u->rol]['color'] ?? '#9ca3af' }};">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="font-semibold text-gray-800">
                                        {{ $u->nombre_completo }}
                                        @if($u->id === auth()->id())
                                            <span class="ml-1 text-xs text-gray-400 font-normal">(tú)</span>
                                        @endif
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-600">{{ $u->email }}</td>

                            <td class="px-6 py-4">
                                @php $info = $rolesLabels[$u->rol] ?? ['label' => $u->rol, 'color' => '#9ca3af']; @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                      style="background-color: {{ $info['color'] }};">
                                    {{ $info['label'] }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Editar --}}
                                    <button type="button"
                                            @click="abrirEditar({ id: {{ $u->id }}, name: '{{ addslashes($u->name) }}', apellido_paterno: '{{ addslashes($u->apellido_paterno) }}', apellido_materno: '{{ addslashes($u->apellido_materno) }}', email: '{{ $u->email }}', rol: '{{ $u->rol }}' })"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                            style="background-color: #D4AF37;"
                                            onmouseover="this.style.backgroundColor='#b8962e'"
                                            onmouseout="this.style.backgroundColor='#D4AF37'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                     m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </button>

                                    {{-- Eliminar --}}
                                    <button type="button"
                                            @click="abrirEliminar({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-600 hover:bg-red-700 text-white transition-colors duration-150">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Eliminar
                                    </button>

                                </div>
                            </td>
                        </tr>
                        @empty
                        @endforelse
                        <tr x-ref="sinResultados" style="display:none;">
                            <td colspan="4" class="px-6 py-10">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                                    <p class="text-sm text-gray-500 max-w-xs mx-auto">No se encontraron usuarios con ese criterio.</p>
                                </div>
                            </td>
                        </tr>
                        @if($usuarios->isEmpty())
                        <tr>
                            <td colspan="4" class="px-6 py-10">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                                    <p class="text-sm text-gray-500 max-w-xs mx-auto">No hay usuarios registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ═══ MODAL CREAR / EDITAR ═══ --}}
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto bg-black/50 backdrop-blur-sm"
         @keydown.escape.window="showModal = false"
         x-cloak>

        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg my-auto">

            <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold" style="color: #0F4229;">
                        <span x-text="editando ? 'Editar usuario' : 'Nuevo usuario'"></span>
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Completa los campos del formulario</p>
                </div>
                <button type="button" @click="showModal = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST"
                  :action="editando ? '/admin/usuarios/' + editando.id : '{{ route('admin.usuarios.store') }}'"
                  class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" x-bind:value="editando ? 'PATCH' : 'POST'">
                <input type="hidden" name="edit_usuario_id" x-bind:value="editando ? editando.id : ''">

                {{-- Nombre(s) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Nombre(s) <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="name" x-model="form.name"
                           placeholder="Ej. Juan Carlos"
                           maxlength="100"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                </div>

                {{-- Apellidos --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                            Apellido paterno <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="apellido_paterno" x-model="form.apellido_paterno"
                               placeholder="Ej. García"
                               maxlength="100"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none"
                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                            Apellido materno
                        </label>
                        <input type="text" name="apellido_materno" x-model="form.apellido_materno"
                               placeholder="Ej. López"
                               maxlength="100"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none"
                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Correo electrónico <span class="text-red-400">*</span>
                    </label>
                    <input type="email" name="email" x-model="form.email"
                           placeholder="Ej. usuario@correo.com"
                           maxlength="150"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <p class="mt-1 text-xs text-gray-400">Debe ser un correo electrónico válido y único en el sistema.</p>
                </div>

                {{-- Contraseña --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Contraseña
                        <span class="text-red-400" x-show="!editando">*</span>
                        <span class="text-gray-400 font-normal normal-case" x-show="editando">(dejar vacío para no cambiar)</span>
                    </label>
                    <div class="relative" x-data="{ showPwd: false }">
                        <input :type="showPwd ? 'text' : 'password'" name="password" x-model="form.password"
                               placeholder="Mínimo 6 caracteres"
                               minlength="6" maxlength="50"
                               class="w-full px-3 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:outline-none"
                               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <button type="button" @click="showPwd = !showPwd"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Rol --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                        Rol <span class="text-red-400">*</span>
                    </label>
                    <select name="rol" x-model="form.rol"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">Selecciona un rol</option>
                        <option value="admin">Administrador</option>
                        <option value="control_escolar">Control Escolar</option>
                        <option value="finanzas">Finanzas</option>
                        <option value="coordinacion">Coordinación Académica</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-bold text-white rounded-lg transition-colors duration-200 shadow-sm"
                            style="background-color: #0F4229;"
                            onmouseover="this.style.backgroundColor='#0a2e1c'"
                            onmouseout="this.style.backgroundColor='#0F4229'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span x-text="editando ? 'Guardar cambios' : 'Crear usuario'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ MODAL CONFIRMAR ELIMINACIÓN ═══ --}}
    <div x-show="showDeleteModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm"
         @keydown.escape.window="showDeleteModal = false"
         x-cloak>

        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Eliminar usuario</h3>
                    <p class="text-sm text-gray-500">Esta acción no se puede deshacer.</p>
                </div>
            </div>

            <p class="text-sm text-gray-600 mb-6">
                ¿Confirmas que deseas eliminar a <strong x-text="deletingName"></strong>?
            </p>

            <form method="POST" :action="'/admin/usuarios/' + deletingId">
                @csrf
                @method('DELETE')
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-bold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                        Sí, eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>

@endsection
