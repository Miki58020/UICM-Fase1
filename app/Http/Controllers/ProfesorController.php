<?php

namespace App\Http\Controllers;

use App\Models\Profesor;
use Illuminate\Http\Request;

class ProfesorController extends Controller
{
    public function index()
    {
        $profesores = Profesor::orderBy('nombre')->get();
        return view('admin.profesores.index', compact('profesores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:150',
            'correo'       => 'required|email|unique:profesores,correo',
            'telefono'     => 'nullable|string|max:20',
            'especialidad' => 'nullable|string|max:100',
        ]);

        Profesor::create([
            'nombre'       => $request->nombre,
            'correo'       => $request->correo,
            'telefono'     => $request->telefono,
            'especialidad' => $request->especialidad,
            'activo'       => true,
        ]);

        return redirect()->route('admin.profesores.index')
            ->with('success', 'Profesor registrado correctamente.');
    }

    public function update(Request $request, Profesor $profesor)
    {
        $request->validate([
            'nombre'       => 'required|string|max:150',
            'correo'       => 'required|email|unique:profesores,correo,' . $profesor->id,
            'telefono'     => 'nullable|string|max:20',
            'especialidad' => 'nullable|string|max:100',
        ]);

        $profesor->update([
            'nombre'       => $request->nombre,
            'correo'       => $request->correo,
            'telefono'     => $request->telefono,
            'especialidad' => $request->especialidad,
        ]);

        return redirect()->route('admin.profesores.index')
            ->with('success', 'Profesor actualizado correctamente.');
    }

    public function toggle(Profesor $profesor)
    {
        $profesor->update(['activo' => !$profesor->activo]);

        return redirect()->back()
            ->with('success', 'Estado del profesor actualizado.');
    }
}
