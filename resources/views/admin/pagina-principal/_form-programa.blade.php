<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    <div>
        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Nombre del programa</label>
        <input type="text" name="nombre" required maxlength="150"
               value="{{ old('nombre', $programa->nombre ?? '') }}"
               placeholder="Ej. Licenciatura en Administración"
               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none"
               onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Nivel</label>
        <select name="nivel" required
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none"
                onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
                onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
            <option value="">Selecciona un nivel</option>
            <option value="licenciatura" {{ old('nivel', $programa->nivel ?? '') === 'licenciatura' ? 'selected' : '' }}>Licenciatura</option>
            <option value="maestria"     {{ old('nivel', $programa->nivel ?? '') === 'maestria'     ? 'selected' : '' }}>Maestría</option>
            <option value="doctorado"    {{ old('nivel', $programa->nivel ?? '') === 'doctorado'    ? 'selected' : '' }}>Doctorado</option>
        </select>
    </div>
</div>
<div>
    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Descripción</label>
    <textarea name="descripcion" rows="2" maxlength="600"
              placeholder="Breve descripción del programa..."
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none resize-none"
              onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
              onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">{{ old('descripcion', $programa->descripcion ?? '') }}</textarea>
</div>
<div>
    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
        Puntos clave
        <span class="normal-case font-normal text-gray-400">(uno por línea)</span>
    </label>
    <textarea name="puntos_clave" rows="4" maxlength="1000"
              placeholder="Intervención educativa en distintos contextos.&#10;Evaluación y seguimiento del desarrollo académico.&#10;Diseño de programas de apoyo a estudiantes."
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none resize-none font-mono"
              onfocus="this.style.borderColor='#0F4229'; this.style.boxShadow='0 0 0 2px rgba(15,66,41,0.20)'"
              onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">{{ old('puntos_clave', isset($programa) ? implode("\n", $programa->puntos_clave ?? []) : '') }}</textarea>
</div>
