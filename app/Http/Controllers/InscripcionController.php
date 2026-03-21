<?php

namespace App\Http\Controllers;

use App\Mail\BienvenidaAlumno;
use App\Models\Alumno;
use App\Models\Aspirante;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InscripcionController extends Controller
{
    public function index()
    {
        $listos = Aspirante::where('estado', 'aprobado')
            ->whereHas('pagos', fn($q) => $q->where('estado', 'aprobado'))
            ->whereHas('alumno', fn($q) => $q->whereNull('user_id'))
            ->with(['programa', 'alumno'])
            ->latest()
            ->get();

        $inscritos = Alumno::whereNotNull('user_id')->count();

        return view('admin.inscripciones.index', compact('listos', 'inscritos'));
    }

    public function inscribir(Alumno $alumno)
    {
        if ($alumno->user_id) {
            return redirect()->route('admin.inscripciones.index')
                ->with('error', 'Este alumno ya tiene acceso al portal.');
        }

        $password = Str::random(8);

        $emailInstitucional = strtolower($alumno->matricula) . '@uicm.edu.mx';

        $user = User::create([
            'name'     => $alumno->nombre_completo,
            'email'    => $emailInstitucional,
            'password' => Hash::make($password),
            'rol'      => 'alumno',
        ]);

        $alumno->update(['user_id' => $user->id]);

        $alumno->load('programa');
        Mail::to($alumno->email)->send(new BienvenidaAlumno($alumno, $password));

        return redirect()->route('admin.inscripciones.resultado', $alumno)
            ->with('temp_password', $password);
    }

    public function resultado(Alumno $alumno)
    {
        $alumno->load(['aspirante', 'programa']);

        return view('admin.inscripciones.generar', [
            'alumno'        => $alumno,
            'temp_password' => session('temp_password'),
        ]);
    }
}
