<?php

namespace App\Http\Controllers;

use App\Models\CargaAcademica;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Profesor;
use App\Models\Programa;
use Illuminate\Http\Request;

class CargaAcademicaController extends Controller
{
    public function index(Request $request)
    {
        $programas = Programa::where('activo', true)->orderBy('nombre')->get();
        $periodos  = Periodo::orderByDesc('fecha_inicio')->get();
        $grupos    = Grupo::with('programa', 'periodo')->orderBy('clave')->get();

        $grupo = null;
        $carga = collect();

        if ($request->filled('grupo_id')) {
            $grupo = Grupo::with([
                'programa',
                'periodo',
                'alumnos',
                'cargaAcademica.materia',
                'cargaAcademica.profesor',
            ])->findOrFail($request->grupo_id);

            $carga = $grupo->cargaAcademica;
        }

        $profesores = Profesor::where('activo', true)->orderBy('nombre')->get();

        return view('admin.carga-academica.index', compact('programas', 'grupos', 'periodos', 'grupo', 'carga', 'profesores'));
    }

    public function generar(Grupo $grupo)
    {
        $materias = Materia::where('programa_id', $grupo->programa_id)
            ->where('cuatrimestre', $grupo->cuatrimestre)
            ->where('activo', true)
            ->get();

        foreach ($materias as $materia) {
            CargaAcademica::firstOrCreate(
                ['grupo_id' => $grupo->id, 'materia_id' => $materia->id, 'periodo_id' => $grupo->periodo_id],
                ['profesor_id' => null, 'horario' => null, 'aula' => null]
            );
        }

        return redirect()
            ->route('admin.carga-academica.index', ['grupo_id' => $grupo->id])
            ->with('success', "Carga académica generada para el grupo {$grupo->clave}.");
    }

    public function actualizar(Request $request, CargaAcademica $carga)
    {
        $request->validate([
            'profesor_id' => 'nullable|exists:profesores,id',
            'horario'     => 'nullable|string|max:50',
            'aula'        => 'nullable|string|max:20',
        ]);

        $carga->update([
            'profesor_id' => $request->profesor_id ?: null,
            'horario'     => $request->horario,
            'aula'        => $request->aula,
        ]);

        return redirect()
            ->route('admin.carga-academica.index', ['grupo_id' => $carga->grupo_id])
            ->with('success', "Asignación actualizada correctamente.");
    }
}
