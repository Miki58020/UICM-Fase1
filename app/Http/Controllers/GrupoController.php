<?php

namespace App\Http\Controllers;

use App\Models\CargaAcademica;
use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\Profesor;
use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos   = Grupo::with('programa', 'periodo')->withCount('alumnos')->orderBy('clave')->get();
        $periodos = Periodo::orderByDesc('fecha_inicio_registro')->get();
        $programas = Programa::where('activo', true)->orderBy('nombre')->get();
        return view('admin.grupos.index', compact('grupos', 'periodos', 'programas'));
    }

    // Vista del profesor: grupos donde tiene materias a su cargo
    public function profesor()
    {
        $profesor = Profesor::where('user_id', Auth::id())->firstOrFail();

        $cargas = CargaAcademica::where('profesor_id', $profesor->id)
            ->with(['materia', 'grupo.programa', 'grupo.alumnos', 'periodo'])
            ->get();

        $grupos = $cargas->groupBy('grupo_id')->map(function ($cargasDelGrupo) {
            $primera = $cargasDelGrupo->first();
            return (object) [
                'grupo'         => $primera->grupo,
                'periodo'       => $primera->periodo,
                'materias'      => $cargasDelGrupo->pluck('materia')->filter(),
                'alumnosActivos' => $primera->grupo?->alumnos->where('estado', 'activo')->count() ?? 0,
            ];
        })->sortBy(fn ($g) => $g->grupo?->clave)->values();

        $totalMaterias = $cargas->count();
        $totalAlumnosActivos = $grupos->sum('alumnosActivos');

        return view('profesor.grupos.index', compact('grupos', 'totalMaterias', 'totalAlumnosActivos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clave'        => 'required|string|max:20|unique:grupos,clave',
            'programa_id'  => 'required|exists:programas,id',
            'periodo_id'   => 'required|exists:periodos,id',
            'cuatrimestre' => ['required', 'integer', 'min:1', $this->reglaCuatrimestreValido($request)],
            'capacidad'    => 'required|integer|min:1|max:500',
        ]);

        Grupo::create($request->only('clave', 'programa_id', 'periodo_id', 'cuatrimestre', 'capacidad'));

        return redirect()->route('admin.grupos.index')
            ->with('success', 'Grupo registrado correctamente.');
    }

    public function update(Request $request, Grupo $grupo)
    {
        $request->validate([
            'clave'        => 'required|string|max:20|unique:grupos,clave,' . $grupo->id,
            'programa_id'  => 'required|exists:programas,id',
            'periodo_id'   => 'required|exists:periodos,id',
            'cuatrimestre' => ['required', 'integer', 'min:1', $this->reglaCuatrimestreValido($request)],
            'capacidad'    => 'required|integer|min:1|max:500',
        ]);

        $grupo->update($request->only('clave', 'programa_id', 'periodo_id', 'cuatrimestre', 'capacidad'));

        return redirect()->route('admin.grupos.index')
            ->with('success', 'Grupo actualizado correctamente.');
    }

    public function destroy(Grupo $grupo)
    {
        if ($grupo->alumnos()->count() > 0) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No se puede eliminar un grupo que tiene alumnos asignados.');
        }

        $grupo->delete();

        return redirect()->route('admin.grupos.index')
            ->with('success', 'Grupo eliminado correctamente.');
    }

    private function reglaCuatrimestreValido(Request $request)
    {
        return function (string $attribute, $value, \Closure $fail) use ($request) {
            $programa = Programa::find($request->programa_id);
            if ($programa && (int) $value > $programa->duracion_cuatrimestres) {
                $fail("El programa seleccionado dura {$programa->duracion_cuatrimestres} cuatrimestres.");
            }
        };
    }
}
