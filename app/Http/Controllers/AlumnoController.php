<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\CargaAcademica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AlumnoController extends Controller
{
    public function listado()
    {
        $alumnos = Alumno::with(['programa', 'grupo'])
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        $programas = \App\Models\Programa::orderBy('nombre')->get();
        $grupos    = \App\Models\Grupo::with('programa')->orderBy('clave')->get();

        $conteo = [
            'total'    => $alumnos->count(),
            'activos'  => $alumnos->where('estado', 'activo')->count(),
            'inactivos'=> $alumnos->where('estado', '!=', 'activo')->count(),
        ];

        return view('admin.alumnos.index', compact('alumnos', 'programas', 'grupos', 'conteo'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'estado'             => 'required|in:activo,inactivo,baja',
            'cuatrimestre_actual'=> 'required|integer|min:1|max:12',
            'grupo_id'           => 'nullable|exists:grupos,id',
            'password'           => 'nullable|string|min:6',
        ], [
            'estado.required'              => 'El estado es obligatorio.',
            'cuatrimestre_actual.required' => 'El cuatrimestre es obligatorio.',
            'cuatrimestre_actual.integer'  => 'El cuatrimestre debe ser un número.',
            'grupo_id.exists'              => 'El grupo seleccionado no es válido.',
            'password.min'                 => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $alumno->update([
            'estado'              => $request->estado,
            'cuatrimestre_actual' => $request->cuatrimestre_actual,
            'grupo_id'            => $request->grupo_id ?: null,
        ]);

        if ($request->filled('password') && $alumno->user_id) {
            $alumno->user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.alumnos.index')
            ->with('success', "Alumno {$alumno->nombre_completo} actualizado correctamente.");
    }

    public function dashboard()
    {
        $alumno = Alumno::where('user_id', Auth::id())
            ->with(['programa', 'grupo', 'pagos'])
            ->firstOrFail();

        $carga = $alumno->grupo_id
            ? CargaAcademica::with(['materia', 'profesor'])
                ->where('grupo_id', $alumno->grupo_id)
                ->get()
            : collect();

        $totalCreditos = $carga->sum(fn($c) => $c->materia->creditos ?? 0);

        return view('alumno.dashboard', compact('alumno', 'carga', 'totalCreditos'));
    }
}
