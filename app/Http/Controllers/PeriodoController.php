<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    public function index()
    {
        $periodos = Periodo::withCount('grupos')->orderByDesc('fecha_inicio_registro')->get();
        return view('admin.periodos.index', compact('periodos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'               => ['required', 'string', 'max:10', 'unique:periodos,nombre', 'regex:/^\d{4}-\d+$/'],
            'label'                => 'required|string|max:80',
            'fecha_inicio_registro'=> 'required|date',
            'fecha_fin_registro'   => 'required|date|after:fecha_inicio_registro',
        ], [
            'nombre.regex'             => 'La clave debe tener el formato año-número (ej: 2026-2).',
            'fecha_fin_registro.after' => 'La fecha de cierre debe ser posterior a la de apertura.',
        ]);

        Periodo::create([
            'nombre'               => $request->nombre,
            'label'                => $request->label,
            'fecha_inicio_registro'=> $request->fecha_inicio_registro,
            'fecha_fin_registro'   => $request->fecha_fin_registro,
            'estado'               => 'inactivo',
            'auto'                 => true,
        ]);

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Periodo creado. Se activará automáticamente en la fecha de apertura.');
    }

    public function update(Request $request, Periodo $periodo)
    {
        $request->validate([
            'nombre'               => ['required', 'string', 'max:10', 'unique:periodos,nombre,' . $periodo->id, 'regex:/^\d{4}-\d+$/'],
            'label'                => 'required|string|max:80',
            'fecha_inicio_registro'=> 'required|date',
            'fecha_fin_registro'   => 'required|date|after:fecha_inicio_registro',
        ], [
            'nombre.regex'             => 'La clave debe tener el formato año-número (ej: 2026-2).',
            'fecha_fin_registro.after' => 'La fecha de cierre debe ser posterior a la de apertura.',
        ]);

        $periodo->update([
            'nombre'               => $request->nombre,
            'label'                => $request->label,
            'fecha_inicio_registro'=> $request->fecha_inicio_registro,
            'fecha_fin_registro'   => $request->fecha_fin_registro,
        ]);

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Periodo actualizado correctamente.');
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
