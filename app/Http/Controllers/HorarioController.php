<?php

namespace App\Http\Controllers;

use App\Models\CargaAcademica;
use App\Models\Profesor;
use Illuminate\Support\Facades\Auth;

class HorarioController extends Controller
{
    public function profesor()
    {
        $profesor = Profesor::where('user_id', Auth::id())->firstOrFail();

        $cargas = CargaAcademica::where('profesor_id', $profesor->id)
            ->with(['materia', 'grupo.programa', 'periodo'])
            ->get()
            ->sortByDesc(fn($c) => $c->periodo_id)
            ->values();

        return view('profesor.horario.index', compact('profesor', 'cargas'));
    }

    public function alumno()
    {
        $alumno = Auth::user()->alumno;

        $cargas = CargaAcademica::with(['materia', 'profesor', 'periodo'])
            ->where('grupo_id', $alumno->grupo_id)
            ->get()
            ->sortBy(fn($c) => $c->materia->nombre ?? '')
            ->values();

        return view('alumno.horario.index', compact('alumno', 'cargas'));
    }
}
