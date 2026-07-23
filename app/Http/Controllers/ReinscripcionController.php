<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\Programa;
use Illuminate\Http\Request;

class ReinscripcionController extends Controller
{
    public function index(Request $request)
    {
        $periodoActivo = Periodo::where('estado', 'activo')->first();

        $todosLosAlumnos = Alumno::where('estado', 'activo')
            ->with(['programa', 'grupo.periodo'])
            ->when($periodoActivo, fn($q) => $q->with([
                'reinscripciones' => fn($q) => $q
                    ->where('concepto', 'cuatrimestre')
                    ->where('periodo', $periodoActivo->nombre),
            ]))
            ->orderBy('apellido_paterno')
            ->orderBy('nombre')
            ->get();

        // Precalcula el estado de reinscripción de cada alumno una sola vez, para no
        // repetir la lógica al filtrar y al pintar la tabla.
        $sinCobro = $enValidacion = $pendienteCompletar = $completadas = 0;
        foreach ($todosLosAlumnos as $alumno) {
            $pagoReinsc = $alumno->reinscripciones->first();
            $enPeriodoActivo = $periodoActivo && $alumno->grupo && $alumno->grupo->periodo_id === $periodoActivo->id;

            if (!$pagoReinsc || $pagoReinsc->estado === 'rechazado') {
                $alumno->estadoReinsc = 'sin_cobro';
                $sinCobro++;
            } elseif ($pagoReinsc->estado === 'pendiente') {
                $alumno->estadoReinsc = 'pendiente';
                $enValidacion++;
            } elseif ($pagoReinsc->estado === 'aprobado' && !$enPeriodoActivo) {
                $alumno->estadoReinsc = 'pendiente_completar';
                $pendienteCompletar++;
            } else {
                $alumno->estadoReinsc = 'completada';
                $completadas++;
            }
        }

        $conteo = compact('sinCobro', 'enValidacion', 'pendienteCompletar', 'completadas');
        $conteo['total'] = $todosLosAlumnos->count();

        $alumnos = $todosLosAlumnos
            ->when($request->filled('q'), function ($col) use ($request) {
                $q = mb_strtolower($request->q);
                return $col->filter(fn ($a) => str_contains(mb_strtolower($a->nombre_completo), $q) || str_contains(mb_strtolower($a->matricula), $q));
            })
            ->when($request->filled('estado'), fn ($col) => $col->where('estadoReinsc', $request->estado))
            ->when($request->filled('programa'), fn ($col) => $col->where('programa_id', (int) $request->programa))
            ->values();

        $pagina = (int) $request->query('page', 1);
        $porPagina = 50;
        $alumnos = new \Illuminate\Pagination\LengthAwarePaginator(
            $alumnos->forPage($pagina, $porPagina)->values(),
            $alumnos->count(),
            $porPagina,
            $pagina,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $gruposActivos = $periodoActivo
            ? Grupo::where('periodo_id', $periodoActivo->id)->orderBy('clave')->get()
            : collect();

        $programas = Programa::orderBy('nombre')->get();

        return view('admin.reinscripciones.index', compact('alumnos', 'periodoActivo', 'gruposActivos', 'programas', 'conteo'));
    }

    public function generarPago(Alumno $alumno)
    {
        $periodoActivo = Periodo::where('estado', 'activo')->firstOrFail();

        $pago = Pago::generarReinscripcion($alumno, $periodoActivo);

        if (!$pago) {
            return redirect()->route('admin.reinscripciones.index')
                ->with('error', 'Este alumno ya tiene un cobro de reinscripción activo para este período.');
        }

        return redirect()->route('admin.reinscripciones.index')
            ->with('success', "Cobro de reinscripción generado para {$alumno->nombre_completo}.");
    }

    public function completar(Request $request, Alumno $alumno)
    {
        $periodoActivo = Periodo::where('estado', 'activo')->firstOrFail();

        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
        ]);

        Pago::where('alumno_id', $alumno->id)
            ->where('concepto', 'cuatrimestre')
            ->where('periodo', $periodoActivo->nombre)
            ->where('estado', 'aprobado')
            ->firstOrFail();

        $grupo = Grupo::findOrFail($request->grupo_id);

        $nuevoCuatrimestre = $alumno->cuatrimestre_actual + 1;

        $alumno->update([
            'grupo_id'            => $grupo->id,
            'cuatrimestre_actual' => $nuevoCuatrimestre,
        ]);

        return redirect()->route('admin.reinscripciones.index')
            ->with('success', "Reinscripción completada para {$alumno->nombre_completo}. Grupo {$grupo->clave}, cuatrimestre {$nuevoCuatrimestre}.");
    }
}
