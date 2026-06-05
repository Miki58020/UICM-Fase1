<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Aspirante;
use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\PeriodoPrograma;
use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodoProgramaController extends Controller
{
    public function index(Periodo $periodo)
    {
        $periodo->load(['programas' => function ($q) {
            $q->withPivot('numero_carrera', 'numero_generacion', 'activo')
              ->orderByPivot('numero_carrera');
        }]);

        $gruposPorPrograma = Grupo::where('periodo_id', $periodo->id)
            ->withCount('alumnos')
            ->get()
            ->groupBy('programa_id');

        $programasDisponibles = Programa::where('activo', true)
            ->whereNotIn('id', $periodo->programas->pluck('id'))
            ->orderBy('nivel')
            ->orderBy('nombre')
            ->get();

        return view('admin.periodos.programas', compact(
            'periodo',
            'programasDisponibles',
            'gruposPorPrograma'
        ));
    }

    public function store(Request $request, Periodo $periodo)
    {
        $request->validate([
            'programa_id' => [
                'required',
                'exists:programas,id',
                Rule::unique('periodo_programa')->where('periodo_id', $periodo->id),
            ],
            'num_grupos' => 'required|integer|min:1|max:10',
        ], [
            'programa_id.unique' => 'Esta carrera ya está agregada a este periodo.',
        ]);

        $programa = Programa::findOrFail($request->programa_id);

        // C = siguiente número dentro de este periodo
        $maxCarrera = PeriodoPrograma::where('periodo_id', $periodo->id)->max('numero_carrera') ?? 0;
        $numeroCarrera = $maxCarrera + 1;

        // G = cuántas veces se ha ofertado esta carrera en periodos anteriores + 1
        $generacionesAnteriores = PeriodoPrograma::where('programa_id', $programa->id)
            ->whereHas('periodo', fn($q) => $q->where('id', '<', $periodo->id))
            ->count();
        $numeroGeneracion = $generacionesAnteriores + 1;

        PeriodoPrograma::create([
            'periodo_id'        => $periodo->id,
            'programa_id'       => $programa->id,
            'numero_carrera'    => $numeroCarrera,
            'numero_generacion' => $numeroGeneracion,
            'activo'            => true,
        ]);

        // Crear grupos automáticamente
        $this->crearGrupos($programa, $periodo, (int) $request->num_grupos);

        return redirect()->route('admin.periodos.index')
            ->with('success', "Carrera \"{$programa->nombre}\" agregada con {$request->num_grupos} grupo(s).")
            ->with('periodos_tab', 'carreras')
            ->with('periodos_tab_periodo', $periodo->id);
    }

    public function toggle(Periodo $periodo, Programa $programa)
    {
        $pp = PeriodoPrograma::where('periodo_id', $periodo->id)
            ->where('programa_id', $programa->id)
            ->firstOrFail();

        $pp->update(['activo' => !$pp->activo]);

        $estado = $pp->activo ? 'activada' : 'pausada';

        return redirect()->route('admin.periodos.index')
            ->with('success', "Carrera \"{$programa->nombre}\" {$estado}.")
            ->with('periodos_tab', 'carreras')
            ->with('periodos_tab_periodo', $periodo->id);
    }

    public function destroy(Periodo $periodo, Programa $programa)
    {
        $pp = PeriodoPrograma::where('periodo_id', $periodo->id)
            ->where('programa_id', $programa->id)
            ->firstOrFail();

        $hayAspirantes = Aspirante::where('programa_id', $programa->id)
            ->where('generacion', $periodo->nombre)
            ->exists();

        $hayAlumnos = Alumno::where('programa_id', $programa->id)
            ->where('periodo_id', $periodo->id)
            ->exists();

        if ($hayAspirantes || $hayAlumnos) {
            return redirect()->route('admin.periodos.index')
                ->with('error', "No se puede eliminar \"{$programa->nombre}\": ya hay aspirantes o alumnos registrados en este periodo.")
                ->with('periodos_tab', 'carreras')
                ->with('periodos_tab_periodo', $periodo->id);
        }

        // Eliminar grupos sin alumnos de este programa en este periodo
        Grupo::where('programa_id', $programa->id)
            ->where('periodo_id', $periodo->id)
            ->whereDoesntHave('alumnos')
            ->delete();

        $pp->delete();

        return redirect()->route('admin.periodos.index')
            ->with('success', "Carrera \"{$programa->nombre}\" eliminada del periodo.")
            ->with('periodos_tab', 'carreras')
            ->with('periodos_tab_periodo', $periodo->id);
    }

    private function crearGrupos(Programa $programa, Periodo $periodo, int $cantidad): void
    {
        $partes      = array_filter(explode('_', $programa->clave));
        $partes      = array_values($partes);
        if (count($partes) >= 3) {
            // 3+ palabras: primera letra de cada una (máx 3)
            $abreviatura = strtoupper($partes[0][0] . $partes[1][0] . $partes[2][0]);
        } elseif (count($partes) === 2) {
            // 2 palabras: 2 letras de la primera + 1 de la segunda
            $abreviatura = strtoupper(substr($partes[0], 0, 2) . $partes[1][0]);
        } else {
            // 1 palabra: primeras 3 letras
            $abreviatura = strtoupper(substr($partes[0], 0, 3));
        }
        [$year, $numPeriodo] = explode('-', $periodo->nombre);
        $letras = range('A', 'Z');

        $existentes = Grupo::where('programa_id', $programa->id)
            ->where('periodo_id', $periodo->id)
            ->count();

        for ($i = 0; $i < $cantidad; $i++) {
            $letra = $letras[$existentes + $i] ?? chr(65 + $existentes + $i);
            $clave = "{$abreviatura}-{$year}-{$numPeriodo}-{$letra}";

            // Si la clave ya existe, añadir sufijo numérico
            if (Grupo::where('clave', $clave)->exists()) {
                $clave = $clave . ($existentes + $i + 1);
            }

            Grupo::create([
                'clave'        => $clave,
                'programa_id'  => $programa->id,
                'periodo_id'   => $periodo->id,
                'cuatrimestre' => 1,
                'capacidad'    => 30,
            ]);
        }
    }
}
