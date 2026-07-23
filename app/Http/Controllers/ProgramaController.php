<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramaController extends Controller
{
    public function index(Request $request)
    {
        $conteo = [
            'total'     => Programa::count(),
            'activos'   => Programa::where('activo', true)->count(),
            'inactivos' => Programa::where('activo', false)->count(),
            'niveles'   => Programa::distinct('nivel')->count('nivel'),
        ];

        $programas = Programa::with('materias')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('clave', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('nivel'), fn ($query) => $query->where('nivel', $request->nivel))
            ->when($request->filled('activo'), fn ($query) => $query->where('activo', $request->activo === 'activo'))
            ->orderBy('nivel')
            ->orderBy('nombre')
            ->paginate(50)
            ->withQueryString();

        return view('admin.programas.index', compact('programas', 'conteo'));
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
