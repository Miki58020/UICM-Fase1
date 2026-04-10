<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\Programa;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos   = Grupo::with('programa', 'periodo')->withCount('alumnos')->orderBy('clave')->get();
        $periodos = Periodo::orderByDesc('fecha_inicio_registro')->get();
        $programas = Programa::where('activo', true)->orderBy('nombre')->get();
        return view('admin.grupos.index', compact('grupos', 'periodos', 'programas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clave'        => 'required|string|max:20|unique:grupos,clave',
            'programa_id'  => 'required|exists:programas,id',
            'periodo_id'   => 'required|exists:periodos,id',
            'cuatrimestre' => 'required|integer|min:1|max:12',
            'capacidad'    => 'required|integer|min:1|max:100',
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
            'cuatrimestre' => 'required|integer|min:1|max:12',
            'capacidad'    => 'required|integer|min:1|max:100',
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
}
