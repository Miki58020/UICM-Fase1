<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Programa;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index()
    {
        $materias  = Materia::with('programa')
            ->orderBy('programa_id')
            ->orderBy('cuatrimestre')
            ->get();

        $programas = Programa::where('activo', true)->orderBy('nombre')->get();

        return view('admin.materias.index', compact('materias', 'programas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clave'        => 'required|string|max:20|unique:materias,clave',
            'nombre'       => 'required|string|max:100',
            'programa_id'  => 'required|exists:programas,id',
            'cuatrimestre' => 'required|integer|min:1|max:12',
            'creditos'     => 'required|integer|min:1|max:20',
        ]);

        Materia::create([
            'clave'        => strtoupper($request->clave),
            'nombre'       => $request->nombre,
            'programa_id'  => $request->programa_id,
            'cuatrimestre' => $request->cuatrimestre,
            'creditos'     => $request->creditos,
            'activo'       => true,
        ]);

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia registrada correctamente.');
    }

    public function update(Request $request, Materia $materia)
    {
        $request->validate([
            'clave'        => 'required|string|max:20|unique:materias,clave,' . $materia->id,
            'nombre'       => 'required|string|max:100',
            'programa_id'  => 'required|exists:programas,id',
            'cuatrimestre' => 'required|integer|min:1|max:12',
            'creditos'     => 'required|integer|min:1|max:20',
        ]);

        $materia->update([
            'clave'        => strtoupper($request->clave),
            'nombre'       => $request->nombre,
            'programa_id'  => $request->programa_id,
            'cuatrimestre' => $request->cuatrimestre,
            'creditos'     => $request->creditos,
        ]);

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia actualizada correctamente.');
    }

    public function toggle(Materia $materia)
    {
        $materia->update(['activo' => !$materia->activo]);

        return redirect()->back()
            ->with('success', 'Estado de la materia actualizado.');
    }
}
