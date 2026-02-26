<?php

namespace App\Http\Controllers;

use App\Models\CargaAcademica;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Programa;
use Illuminate\Http\Request;

class CargaAcademicaController extends Controller
{
    public function index(Request $request)
    {
        $programas = Programa::where('activo', true)->orderBy('nombre')->get();
        $grupos    = Grupo::with('programa')->orderBy('clave')->get();
        $ciclos    = Grupo::distinct()->orderByDesc('ciclo')->pluck('ciclo');

        $grupo = null;
        $carga = collect();

        if ($request->filled('grupo_id')) {
            $grupo = Grupo::with([
                'programa',
                'alumnos',
                'cargaAcademica.materia',
                'cargaAcademica.profesor',
            ])->findOrFail($request->grupo_id);

            $carga = $grupo->cargaAcademica;
        }

        return view('admin.carga-academica.index', compact('programas', 'grupos', 'ciclos', 'grupo', 'carga'));
    }

    public function generar(Grupo $grupo)
    {
        $materias = Materia::where('programa_id', $grupo->programa_id)
            ->where('cuatrimestre', $grupo->cuatrimestre)
            ->where('activo', true)
            ->get();

        foreach ($materias as $materia) {
            CargaAcademica::firstOrCreate(
                ['grupo_id' => $grupo->id, 'materia_id' => $materia->id],
                ['ciclo' => $grupo->ciclo, 'profesor_id' => null, 'horario' => null, 'aula' => null]
            );
        }

        return redirect()
            ->route('admin.carga-academica.index', ['grupo_id' => $grupo->id])
            ->with('success', "Carga académica generada para el grupo {$grupo->clave}.");
    }
}
