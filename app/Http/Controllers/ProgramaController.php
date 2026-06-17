<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramaController extends Controller
{
    public function index()
    {
        $programas = Programa::with('materias')->orderBy('nivel')->orderBy('nombre')->get();
        return view('admin.programas.index', compact('programas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'                 => 'required|string|max:120',
            'clave'                  => 'required|string|max:10|unique:programas,clave|alpha_dash',
            'nivel'                  => 'required|in:licenciatura,maestria,doctorado',
            'duracion_cuatrimestres' => 'required|integer|min:1|max:20',
            'total_creditos'         => 'nullable|integer|min:1|max:999',
        ], [
            'clave.alpha_dash' => 'La clave solo puede contener letras, números y guiones bajos.',
            'clave.unique'     => 'Esa clave ya está en uso.',
        ]);

        Programa::create([
            'nombre'                 => $request->nombre,
            'clave'                  => strtolower($request->clave),
            'nivel'                  => $request->nivel,
            'duracion_cuatrimestres' => $request->duracion_cuatrimestres,
            'total_creditos'         => $request->total_creditos ?: null,
            'activo'                 => true,
        ]);

        return redirect()->route('admin.programas.index')
            ->with('success', "Programa \"{$request->nombre}\" creado correctamente.");
    }

    public function update(Request $request, Programa $programa)
    {
        $request->validate([
            'nombre'                 => 'required|string|max:120',
            'nivel'                  => 'required|in:licenciatura,maestria,doctorado',
            'duracion_cuatrimestres' => 'required|integer|min:1|max:20',
            'total_creditos'         => 'nullable|integer|min:1|max:999',
        ]);

        $programa->update([
            'nombre'                 => $request->nombre,
            'nivel'                  => $request->nivel,
            'duracion_cuatrimestres' => $request->duracion_cuatrimestres,
            'total_creditos'         => $request->total_creditos ?: null,
        ]);

        return redirect()->route('admin.programas.index')
            ->with('success', "Programa \"{$programa->nombre}\" actualizado.");
    }

    public function toggle(Programa $programa)
    {
        $programa->update(['activo' => !$programa->activo]);

        $estado = $programa->activo ? 'activado' : 'desactivado';
        return redirect()->route('admin.programas.index')
            ->with('success', "Programa \"{$programa->nombre}\" {$estado}.");
    }
}
