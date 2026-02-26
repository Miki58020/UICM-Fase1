<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\CargaAcademica;
use Illuminate\Support\Facades\Auth;

class AlumnoController extends Controller
{
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
