<div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-700">{{ $titulo }}</h2>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-uicm-green-pale text-uicm-green">{{ $lista->count() }} programa{{ $lista->count() !== 1 ? 's' : '' }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Nombre</th>
                    <th class="px-6 py-3">Clave</th>
                    <th class="px-6 py-3">Nivel</th>
                    <th class="px-6 py-3 text-center">Duración</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($lista as $programa)
                @php
                    $nivelCfg = [
                        'licenciatura' => ['badge' => 'Licenciatura', 'color' => '#0F4229'],
                        'maestria'     => ['badge' => 'Maestría',     'color' => '#D4AF37'],
                        'doctorado'    => ['badge' => 'Doctorado',    'color' => '#EFAD5A'],
                    ];
                    $cfg = $nivelCfg[$programa->nivel] ?? ['badge' => ucfirst($programa->nivel), 'color' => '#9ca3af'];
                @endphp
                <tr class="hover:bg-gray-50 transition-colors duration-100 {{ !$programa->activo ? 'opacity-60' : '' }}">
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
                        <button onclick="abrirEditar({{ $programa->id }}, '{{ addslashes($programa->nombre) }}', '{{ $programa->clave }}', '{{ $programa->nivel }}', {{ $programa->duracion_cuatrimestres }})"
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
                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                        No hay programas en esta categoría.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
