<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\Programa;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    public function index()
    {
        $periodos = Periodo::withCount('grupos')
            ->with(['programas' => fn($q) => $q->withPivot('numero_carrera', 'numero_generacion', 'activo')->orderByPivot('numero_carrera')])
            ->orderByDesc('fecha_inicio_registro')
            ->get();

        $programasActivos = Programa::where('activo', true)
            ->orderBy('nivel')->orderBy('nombre')->get();

        $gruposPorPeriodo = Grupo::withCount('alumnos')
            ->get()->groupBy('periodo_id');

        $tabActivo = session('periodos_tab', 'periodos');

        return view('admin.periodos.index', compact('periodos', 'programasActivos', 'gruposPorPeriodo', 'tabActivo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'               => ['required', 'string', 'max:10', 'unique:periodos,nombre', 'regex:/^\d{4}-\d+$/'],
            'label'                => 'required|string|max:80',
            'fecha_inicio_clases'  => 'nullable|date',
            'fecha_fin_clases'     => 'nullable|date|after_or_equal:fecha_inicio_clases',
        ], [
            'nombre.regex' => 'La clave debe tener el formato año-número (ej: 2026-2).',
        ]);

        Periodo::create([
            'nombre'               => $request->nombre,
            'label'                => $request->label,
            'fecha_inicio_clases'  => $request->fecha_inicio_clases ?: null,
            'fecha_fin_clases'     => $request->fecha_fin_clases ?: null,
            'fecha_inicio_registro'=> null,
            'fecha_fin_registro'   => null,
            'estado'               => 'inactivo',
            'auto'                 => false,
        ]);

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Periodo creado. Se activará automáticamente en la fecha de apertura.');
    }

    public function update(Request $request, Periodo $periodo)
    {
        $request->validate([
            'nombre'              => ['required', 'string', 'max:10', 'unique:periodos,nombre,' . $periodo->id, 'regex:/^\d{4}-\d+$/'],
            'label'               => 'required|string|max:80',
            'fecha_inicio_clases' => 'nullable|date',
            'fecha_fin_clases'    => 'nullable|date|after_or_equal:fecha_inicio_clases',
        ], [
            'nombre.regex' => 'La clave debe tener el formato año-número (ej: 2026-2).',
        ]);

        $periodo->update([
            'nombre'              => $request->nombre,
            'label'               => $request->label,
            'fecha_inicio_clases' => $request->fecha_inicio_clases ?: null,
            'fecha_fin_clases'    => $request->fecha_fin_clases ?: null,
        ]);

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Periodo actualizado correctamente.');
    }

    public function configurarInscripcion(Request $request, Periodo $periodo)
    {
        $request->validate([
            'fecha_inicio_registro' => 'required|date',
            'fecha_fin_registro'    => 'required|date|after:fecha_inicio_registro',
        ], [
            'fecha_fin_registro.after' => 'La fecha de cierre debe ser posterior a la apertura.',
        ]);

        $periodo->update([
            'fecha_inicio_registro' => $request->fecha_inicio_registro,
            'fecha_fin_registro'    => $request->fecha_fin_registro,
            'auto'                  => true,
        ]);

        return redirect()->route('admin.periodos.index')
            ->with('success', "Periodo de inscripción configurado para \"{$periodo->label}\".")
            ->with('periodos_tab', 'inscripciones');
    }

    public function activar(Periodo $periodo)
    {
        $periodo->update(['estado' => 'activo', 'auto' => false]);

        return redirect()->route('admin.periodos.index')
            ->with('success', "Periodo \"{$periodo->label}\" activado manualmente.");
    }

    public function cerrar(Periodo $periodo)
    {
        $periodo->update(['estado' => 'cerrado', 'auto' => false]);

        return redirect()->route('admin.periodos.index')
            ->with('success', "Periodo \"{$periodo->label}\" cerrado manualmente.");
    }

    public function toggleAuto(Periodo $periodo)
    {
        $periodo->update(['auto' => ! $periodo->auto]);

        $msg = $periodo->auto
            ? "Periodo \"{$periodo->label}\" cambiado a modo automático."
            : "Periodo \"{$periodo->label}\" cambiado a modo manual.";

        return redirect()->route('admin.periodos.index')->with('success', $msg);
    }

    public function destroy(Periodo $periodo)
    {
        if ($periodo->grupos()->exists()) {
            return redirect()->route('admin.periodos.index')
                ->with('error', "No se puede eliminar el periodo \"{$periodo->label}\" porque tiene grupos asociados.");
        }

        $label = $periodo->label;
        $periodo->delete();

        return redirect()->route('admin.periodos.index')
            ->with('success', "Periodo \"{$label}\" eliminado correctamente.");
    }
}
