<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    public function index()
    {
        $periodos = Periodo::withCount('grupos')->orderByDesc('fecha_inicio')->get();
        return view('admin.periodos.index', compact('periodos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:10|unique:periodos,nombre',
            'label'        => 'required|string|max:80',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after:fecha_inicio',
        ]);

        Periodo::create([
            'nombre'       => $request->nombre,
            'label'        => $request->label,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin,
            'estado'       => 'proximo',
        ]);

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Periodo registrado correctamente.');
    }

    public function update(Request $request, Periodo $periodo)
    {
        $request->validate([
            'nombre'       => 'required|string|max:10|unique:periodos,nombre,' . $periodo->id,
            'label'        => 'required|string|max:80',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after:fecha_inicio',
        ]);

        $periodo->update([
            'nombre'       => $request->nombre,
            'label'        => $request->label,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin,
        ]);

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Periodo actualizado correctamente.');
    }

    public function activar(Periodo $periodo)
    {
        // Cierra todos los demás periodos que estaban activos
        Periodo::where('estado', 'activo')->where('id', '!=', $periodo->id)
            ->update(['estado' => 'cerrado']);

        $periodo->update(['estado' => 'activo']);

        return redirect()->route('admin.periodos.index')
            ->with('success', "El periodo \"{$periodo->label}\" está ahora activo.");
    }

    public function cerrar(Periodo $periodo)
    {
        $periodo->update(['estado' => 'cerrado']);

        return redirect()->route('admin.periodos.index')
            ->with('success', "El periodo \"{$periodo->label}\" fue cerrado.");
    }
}
