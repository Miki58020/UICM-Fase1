<?php

namespace App\Http\Controllers;

use App\Models\AclaracionCalificacion;
use App\Models\Alumno;
use App\Models\CargaAcademica;
use App\Models\Calificacion;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AclaracionCalificacionController extends Controller
{
    // El profesor solicita corregir una calificación ya cerrada
    public function solicitar(Request $request, CargaAcademica $carga, Alumno $alumno)
    {
        $profesor = Profesor::where('user_id', Auth::id())->firstOrFail();
        abort_if($carga->profesor_id !== $profesor->id, 403);
        abort_if($alumno->grupo_id !== $carga->grupo_id, 404);

        $request->validate([
            'tipo' => 'required|in:final,extraordinario',
            'calificacion_propuesta' => 'required|numeric|min:0|max:10',
            'motivo' => 'required|string|max:500',
        ], [
            'calificacion_propuesta.required' => 'Indica la calificación correcta.',
            'motivo.required' => 'Indica el motivo de la aclaración.',
        ]);

        if ($request->tipo === 'final') {
            $tieneExtraordinario = Calificacion::where('alumno_id', $alumno->id)
                ->where('carga_academica_id', $carga->id)
                ->where('tipo', 'extraordinario')
                ->exists();

            if ($tieneExtraordinario) {
                return back()->withErrors(['Este alumno ya presentó examen extraordinario: la calificación que cuenta es la del extraordinario, no la final. Solicita la aclaración sobre el extraordinario.']);
            }
        }

        $existePendiente = AclaracionCalificacion::where('alumno_id', $alumno->id)
            ->where('carga_academica_id', $carga->id)
            ->where('tipo', $request->tipo)
            ->where('estado', 'pendiente')
            ->exists();

        if ($existePendiente) {
            return back()->withErrors(['Ya hay una aclaración pendiente para este alumno y materia.']);
        }

        AclaracionCalificacion::create([
            'alumno_id' => $alumno->id,
            'carga_academica_id' => $carga->id,
            'profesor_id' => $profesor->id,
            'tipo' => $request->tipo,
            'calificacion_propuesta' => round((float) $request->calificacion_propuesta, 1),
            'motivo' => $request->motivo,
            'estado' => 'pendiente',
        ]);

        return back()->with('success', 'Aclaración enviada a control escolar.');
    }

    // El profesor consulta el estado de las aclaraciones que ha solicitado
    public function misAclaraciones()
    {
        $profesor = Profesor::where('user_id', Auth::id())->firstOrFail();

        $aclaraciones = AclaracionCalificacion::where('profesor_id', $profesor->id)
            ->with(['alumno', 'cargaAcademica.materia', 'cargaAcademica.grupo'])
            ->orderByDesc('created_at')
            ->get();

        return view('profesor.aclaraciones.index', compact('aclaraciones'));
    }

    // Control escolar: bandeja de aclaraciones
    public function index()
    {
        $aclaraciones = AclaracionCalificacion::with(['alumno', 'profesor', 'cargaAcademica.materia', 'cargaAcademica.grupo'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.aclaraciones.index', compact('aclaraciones'));
    }

    public function aprobar(AclaracionCalificacion $aclaracion)
    {
        abort_if($aclaracion->estado !== 'pendiente', 422, 'Esta aclaración ya fue resuelta.');

        Calificacion::updateOrCreate(
            [
                'alumno_id' => $aclaracion->alumno_id,
                'carga_academica_id' => $aclaracion->carga_academica_id,
                'tipo' => $aclaracion->tipo,
            ],
            ['calificacion' => $aclaracion->calificacion_propuesta]
        );

        $aclaracion->update([
            'estado' => 'aprobada',
            'revisado_por' => Auth::id(),
            'revisado_at' => now(),
        ]);

        $aclaracion->alumno->recalcularCreditosAcumulados();

        return back()->with('success', 'Aclaración aprobada. La calificación fue corregida.');
    }

    public function rechazar(Request $request, AclaracionCalificacion $aclaracion)
    {
        abort_if($aclaracion->estado !== 'pendiente', 422, 'Esta aclaración ya fue resuelta.');

        $request->validate(['motivo_rechazo' => 'required|string|max:500'], [
            'motivo_rechazo.required' => 'Indica el motivo del rechazo.',
        ]);

        $aclaracion->update([
            'estado' => 'rechazada',
            'motivo_rechazo' => $request->motivo_rechazo,
            'revisado_por' => Auth::id(),
            'revisado_at' => now(),
        ]);

        return back()->with('success', 'Aclaración rechazada.');
    }
}
