<div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="h-1.5 w-full" style="background-color: #0F4229;"></div>
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-700">{{ $titulo }}</h2>
        <span class="text-xs text-gray-400">{{ $programas->count() }} carrera{{ $programas->count() !== 1 ? 's' : '' }}</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Carrera</th>
                    <th class="px-6 py-3 text-center" title="Número de carrera en este periodo (C de la matrícula)">C</th>
                    <th class="px-6 py-3 text-center" title="Número de generación (G de la matrícula)">G</th>
                    <th class="px-6 py-3 text-center">Grupos</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($programas as $programa)
                @php
                    $grupos   = $gruposPorPrograma[$programa->id] ?? collect();
                    $totalIns = $grupos->sum('alumnos_count');
                    $totalCap = $grupos->sum('capacidad');
                @endphp
                <tr class="hover:bg-gray-50 transition-colors duration-100">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800">{{ $programa->nombre }}</p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $programa->clave }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-mono font-bold text-gray-700">{{ $programa->pivot->numero_carrera }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-mono font-bold text-gray-700">{{ $programa->pivot->numero_generacion }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($grupos->isNotEmpty())
                            <span class="font-semibold text-gray-700">{{ $grupos->count() }}</span>
                            <span class="text-gray-400 text-xs"> ({{ $totalIns }}/{{ $totalCap }})</span>
                        @else
                            <span class="text-gray-400 text-xs italic">Sin grupos</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form method="POST" action="{{ route('admin.periodos.programas.toggle', [$periodo->id, $programa->id]) }}">
                            @csrf @method('PATCH')
                            @if($programa->pivot->activo)
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-white transition-colors duration-150"
                                    style="background-color: #0F4229;"
                                    onmouseover="this.style.backgroundColor='#0a2e1c'"
                                    onmouseout="this.style.backgroundColor='#0F4229'">
                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                Activa
                            </button>
                            @else
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold text-white transition-colors duration-150"
                                    style="background-color: #EFAD5A;"
                                    onmouseover="this.style.backgroundColor='#d4922e'"
                                    onmouseout="this.style.backgroundColor='#EFAD5A'">
                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80 inline-block"></span>
                                Pausada
                            </button>
                            @endif
                        </form>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form method="POST"
                              action="{{ route('admin.periodos.programas.destroy', [$periodo->id, $programa->id]) }}"
                              onsubmit="return confirm('¿Quitar {{ addslashes($programa->nombre) }} de este periodo? Se eliminarán los grupos sin alumnos.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border border-red-200 text-red-500 hover:bg-red-50 transition-colors duration-150">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Quitar
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                        No hay carreras en esta categoría.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
