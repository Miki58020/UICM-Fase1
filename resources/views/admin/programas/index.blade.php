@extends('layouts.app')

@section('title', 'Programas académicos | UICM')

@section('content')

<section
    x-data="{
        busqueda: '',
        filtroNivel: '',
        filtroActivo: '',
        filtrar() {
            this.$nextTick(() => {
                const filas = this.$refs.tbody.querySelectorAll('tr[data-nombre]');
                let visibles = 0;
                filas.forEach(f => {
                    const texto = this.busqueda.toLowerCase();
                    const pasaBusqueda = !texto || f.dataset.nombre.includes(texto) || f.dataset.clave.includes(texto);
                    const pasaNivel    = !this.filtroNivel   || f.dataset.nivel   === this.filtroNivel;
                    const pasaActivo   = !this.filtroActivo  || f.dataset.activo  === this.filtroActivo;
                    const mostrar = pasaBusqueda && pasaNivel && pasaActivo;
                    f.style.display = mostrar ? '' : 'none';
                    if (mostrar) visibles++;
                });
                this.$refs.sinResultados.style.display = visibles === 0 ? '' : 'none';
                this.$refs.contadorVisible.textContent = visibles + ' registro' + (visibles !== 1 ? 's' : '');
            });
        }
    }"
    class="bg-uicm-gray min-h-screen py-12 px-4">

<div class="container mx-auto px-4 lg:px-12 max-w-7xl">

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #D4AF37;">
                Coordinación Académica
            </p>
            <h1 class="text-2xl font-extrabold text-gray-900">Programas académicos</h1>
            <div class="w-14 h-1 rounded-full mt-2" style="background-color: #D4AF37;"></div>
        </div>
        <button onclick="document.getElementById('modal-crear').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm transition-colors duration-200 self-start sm:self-auto"
                style="background-color: #0F4229;"
                onmouseover="this.style.backgroundColor='#0a2e1c'"
                onmouseout="this.style.backgroundColor='#0F4229'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo programa
        </button>
    </div>

    {{-- Cards resumen --}}
    @php
        $total     = $programas->count();
        $activos   = $programas->where('activo', true)->count();
        $inactivos = $programas->where('activo', false)->count();
        $niveles   = $programas->groupBy('nivel')->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #0F4229;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #0F4229;">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #D4AF37;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Activos</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #D4AF37;">{{ $activos }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #EFAD5A;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Inactivos</p>
            <p class="text-2xl font-extrabold mt-1" style="color: #EFAD5A;">{{ $inactivos }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm px-5 py-4 border-l-4" style="border-color: #9ca3af;">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Niveles</p>
            <p class="text-2xl font-extrabold mt-1 text-gray-500">{{ $niveles }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input type="text" x-model="busqueda" @input="filtrar()"
                   placeholder="Buscar por nombre o clave…"
                   class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none"
                   onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                   onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
        </div>
        <div class="relative sm:w-52">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <select x-model="filtroNivel" @change="filtrar()"
                    class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                <option value="">Todos los niveles</option>
                <option value="licenciatura">Licenciatura</option>
                <option value="maestria">Maestría</option>
                <option value="doctorado">Doctorado</option>
            </select>
        </div>
        <div class="relative sm:w-44">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <select x-model="filtroActivo" @change="filtrar()"
                    class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-white focus:outline-none appearance-none"
                    onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="inactivo">Inactivos</option>
            </select>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Listado de programas</h2>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green" x-ref="contadorVisible">{{ $total }} registro{{ $total !== 1 ? 's' : '' }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Clave</th>
                        <th class="px-6 py-3">Nivel</th>
                        <th class="px-6 py-3 text-center">Duración</th>
                        <th class="px-6 py-3 text-center">Créditos plan</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-ref="tbody">
                    @php
                        $nivelCfg = [
                            'licenciatura' => ['badge' => 'Licenciatura', 'color' => '#0F4229'],
                            'maestria'     => ['badge' => 'Maestría',     'color' => '#D4AF37'],
                            'doctorado'    => ['badge' => 'Doctorado',    'color' => '#EFAD5A'],
                        ];
                    @endphp
                    @forelse($programas as $programa)
                    @php $cfg = $nivelCfg[$programa->nivel] ?? ['badge' => ucfirst($programa->nivel), 'color' => '#9ca3af']; @endphp
                    <tr data-nombre="{{ strtolower($programa->nombre) }}"
                        data-clave="{{ strtolower($programa->clave) }}"
                        data-nivel="{{ $programa->nivel }}"
                        data-activo="{{ $programa->activo ? 'activo' : 'inactivo' }}"
                        class="hover:bg-gray-50 transition-colors duration-100 {{ !$programa->activo ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $programa->nombre }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $programa->clave }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold text-white"
                                  style="background-color: {{ $cfg['color'] }};">
                                {{ $cfg['badge'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $programa->duracion_cuatrimestres }} cuatrimestres</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $definidos = $programa->materias->sum('creditos');
                                $total_c   = $programa->total_creditos;
                            @endphp
                            @if($total_c)
                                <span class="text-sm font-semibold {{ $definidos >= $total_c ? 'text-uicm-green' : 'text-uicm-orange' }}">
                                    {{ $definidos }} / {{ $total_c }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form method="POST" action="{{ route('admin.programas.toggle', $programa->id) }}">
                                @csrf @method('PATCH')
                                @if($programa->activo)
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-white transition-colors duration-150"
                                        style="background-color: #0F4229;"
                                        onmouseover="this.style.backgroundColor='#0a2e1c'"
                                        onmouseout="this.style.backgroundColor='#0F4229'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                    Activo
                                </button>
                                @else
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-gray-600 bg-gray-200 hover:bg-gray-300 transition-colors duration-150">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                    Inactivo
                                </button>
                                @endif
                            </form>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="abrirEditar({{ $programa->id }}, '{{ addslashes($programa->nombre) }}', '{{ $programa->clave }}', '{{ $programa->nivel }}', {{ $programa->duracion_cuatrimestres }}, {{ $programa->total_creditos ?? 'null' }})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors duration-150"
                                    style="background-color: #D4AF37;"
                                    onmouseover="this.style.backgroundColor='#b8962e'"
                                    onmouseout="this.style.backgroundColor='#D4AF37'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                                <p class="text-sm text-gray-500 max-w-xs mx-auto">No hay programas registrados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    <tr x-ref="sinResultados" style="display:none;">
                        <td colspan="7" class="px-6 py-8">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #f3f4f6;">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-extrabold text-gray-900 mb-1">Sin resultados</h3>
                                <p class="text-sm text-gray-500 max-w-xs mx-auto">No hay programas con ese criterio de búsqueda.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
</section>

{{-- Modal crear --}}
<div id="modal-crear" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Nuevo programa</h3>
            <button onclick="document.getElementById('modal-crear').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.programas.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej. Ingeniería en Sistemas"
                       maxlength="120"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                       required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Clave <span class="text-red-500">*</span></label>
                    <input type="text" name="clave" value="{{ old('clave') }}" placeholder="Ej. ing_sistemas"
                           maxlength="10"
                           oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '')"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none font-mono"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                           required>
                    <p class="text-xs text-gray-400 mt-1">Solo letras, números y guión bajo. Máx. 10 caracteres.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nivel <span class="text-red-500">*</span></label>
                    <select name="nivel"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                            required>
                        <option value="">Seleccionar</option>
                        <option value="licenciatura" {{ old('nivel') === 'licenciatura' ? 'selected' : '' }}>Licenciatura</option>
                        <option value="maestria"     {{ old('nivel') === 'maestria'     ? 'selected' : '' }}>Maestría</option>
                        <option value="doctorado"    {{ old('nivel') === 'doctorado'    ? 'selected' : '' }}>Doctorado</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duración (cuatrimestres) <span class="text-red-500">*</span></label>
                    <input type="number" name="duracion_cuatrimestres" value="{{ old('duracion_cuatrimestres', 12) }}"
                           min="1" max="20"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total de créditos del plan</label>
                    <input type="number" name="total_creditos" value="{{ old('total_creditos') }}"
                           min="1" max="999" placeholder="Ej. 216"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    <p class="text-xs text-gray-400 mt-1">Créditos totales que debe cubrir el alumno.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-crear').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
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

{{-- Modal editar --}}
<div id="modal-editar" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="h-1.5 w-full rounded-t-2xl" style="background-color: #0F4229;"></div>
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Editar programa</h3>
            <button onclick="document.getElementById('modal-editar').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-editar" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                <input type="text" id="edit-nombre" name="nombre" maxlength="120"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                       required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Clave</label>
                <input type="text" id="edit-clave" disabled
                       class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-400 font-mono cursor-not-allowed">
                <p class="text-xs text-gray-400 mt-1">La clave no se puede cambiar para no afectar registros existentes.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nivel <span class="text-red-500">*</span></label>
                    <select id="edit-nivel" name="nivel"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                            onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                            required>
                        <option value="licenciatura">Licenciatura</option>
                        <option value="maestria">Maestría</option>
                        <option value="doctorado">Doctorado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duración (cuatrimestres) <span class="text-red-500">*</span></label>
                    <input type="number" id="edit-duracion" name="duracion_cuatrimestres" min="1" max="20"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                           required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total de créditos del plan</label>
                <input type="number" id="edit-total-creditos" name="total_creditos" min="1" max="999" placeholder="Ej. 216"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                       onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                <p class="text-xs text-gray-400 mt-1">Créditos totales que debe cubrir el alumno.</p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-editar').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
                        style="background-color: #D4AF37;"
                        onmouseover="this.style.backgroundColor='#b8962e'"
                        onmouseout="this.style.backgroundColor='#D4AF37'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirEditar(id, nombre, clave, nivel, duracion, totalCreditos) {
    document.getElementById('form-editar').action = '/admin/programas/' + id;
    document.getElementById('edit-nombre').value          = nombre;
    document.getElementById('edit-clave').value           = clave;
    document.getElementById('edit-nivel').value           = nivel;
    document.getElementById('edit-duracion').value        = duracion;
    document.getElementById('edit-total-creditos').value  = totalCreditos || '';
    document.getElementById('modal-editar').classList.remove('hidden');
}
@if($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('modal-crear').classList.remove('hidden');
});
@endif
</script>
@endpush
