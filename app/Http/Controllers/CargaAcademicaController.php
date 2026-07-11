<?php

namespace App\Http\Controllers;

use App\Models\CargaAcademica;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Profesor;
use App\Models\Programa;
use Illuminate\Http\Request;

class CargaAcademicaController extends Controller
{
    public function index(Request $request)
    {
        $programas = Programa::where('activo', true)->orderBy('nombre')->get();
        $periodos  = Periodo::orderByDesc('fecha_inicio_registro')->get();
        $grupos    = Grupo::with('programa', 'periodo')->orderBy('clave')->get();

        $grupo = null;
        $carga = collect();

        if ($request->filled('grupo_id')) {
            $grupo = Grupo::with([
                'programa',
                'periodo',
                'alumnos',
                'cargaAcademica.materia',
                'cargaAcademica.profesor',
            ])->findOrFail($request->grupo_id);

            $carga = $grupo->cargaAcademica;
        }

        $profesores = Profesor::where('activo', true)->orderBy('nombre')->get();

        return view('admin.carga-academica.index', compact('programas', 'grupos', 'periodos', 'grupo', 'carga', 'profesores'));
    }

    public function generar(Grupo $grupo)
    {
        $this->generarCargaAcademicaParaGrupo($grupo);

        return redirect()
            ->route('admin.carga-academica.index', ['grupo_id' => $grupo->id])
            ->with('success', "Carga académica generada para el grupo {$grupo->clave}.");
    }

    private function generarCargaAcademicaParaGrupo(Grupo $grupo): void
    {
        $materias = Materia::where('programa_id', $grupo->programa_id)
            ->where('cuatrimestre', $grupo->cuatrimestre)
            ->where('activo', true)
            ->get();

        foreach ($materias as $materia) {
            CargaAcademica::firstOrCreate(
                ['grupo_id' => $grupo->id, 'materia_id' => $materia->id, 'periodo_id' => $grupo->periodo_id],
                ['profesor_id' => null, 'horario' => null, 'aula' => null, 'estado_revision' => null]
            );
        }
    }

    public function actualizar(Request $request, CargaAcademica $carga)
    {
        $request->validate([
            'profesor_id'  => 'nullable|exists:profesores,id',
            'horario'      => 'nullable|string|max:50',
            'aula'         => 'nullable|string|max:100',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        if ($request->fecha_inicio && $request->fecha_fin) {
            $traslape = CargaAcademica::where('grupo_id', $carga->grupo_id)
                ->where('id', '!=', $carga->id)
                ->whereNotNull('fecha_inicio')
                ->whereNotNull('fecha_fin')
                ->where('fecha_inicio', '<=', $request->fecha_fin)
                ->where('fecha_fin', '>=', $request->fecha_inicio)
                ->with('materia')
                ->first();

            if ($traslape) {
                return back()->withErrors([
                    "Las fechas se traslapan con \"{$traslape->materia->nombre}\" en el mismo grupo.",
                ]);
            }
        }

        $carga->update([
            'profesor_id'  => $request->profesor_id ?: null,
            'horario'      => $request->horario,
            'aula'         => $request->aula,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin,
        ]);

        return redirect()
            ->route('admin.carga-academica.index', ['grupo_id' => $carga->grupo_id])
            ->with('success', "Asignación actualizada correctamente.");
    }

    public function plantillaHorarios()
    {
        $columnas = [
            'grupo_clave', 'materia_clave', 'profesor_correo', 'horario', 'aula', 'fecha_inicio', 'fecha_fin',
        ];

        $ejemplo = [
            'LEI-2026-2-A', 'ING-101', 'profesor@uicm.edu.mx', 'Sábados - Enero 2026', 'Sala Zoom 1', '2026-01-10', '2026-02-01',
        ];

        $filas = [$columnas, $ejemplo];

        $callback = function () use ($filas) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM para acentos en Excel
            foreach ($filas as $fila) {
                fputcsv($handle, $fila);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_horarios.csv"',
        ]);
    }

    public function importarHorarios(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');
        $encabezados = array_map(fn($h) => strtolower(trim($h)), fgetcsv($handle, 0, ',', '"', '\\') ?: []);

        $filas = [];
        $numeroLinea = 1;
        while (($valores = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $numeroLinea++;
            if (count(array_filter($valores, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $fila = [];
            foreach ($encabezados as $i => $columna) {
                $fila[$columna] = isset($valores[$i]) ? trim((string) $valores[$i]) : null;
            }
            $filas[] = ['linea' => $numeroLinea, 'datos' => $fila];
        }
        fclose($handle);

        $errores = [];
        $validas = [];
        $ventanasPorGrupo = [];

        foreach ($filas as $fila) {
            $d = $fila['datos'];
            $linea = $fila['linea'];

            $faltantes = array_filter(
                ['grupo_clave', 'materia_clave'],
                fn($campo) => empty($d[$campo] ?? null)
            );
            if (!empty($faltantes)) {
                $errores[] = "Línea {$linea}: faltan los campos (" . implode(', ', $faltantes) . ").";
                continue;
            }

            $grupo = Grupo::where('clave', $d['grupo_clave'])->first();
            if (!$grupo) {
                $errores[] = "Línea {$linea}: no existe el grupo \"{$d['grupo_clave']}\".";
                continue;
            }

            $materia = Materia::where('clave', $d['materia_clave'])->first();
            if (!$materia) {
                $errores[] = "Línea {$linea}: no existe la materia \"{$d['materia_clave']}\".";
                continue;
            }

            $carga = CargaAcademica::where('grupo_id', $grupo->id)->where('materia_id', $materia->id)->first();
            if (!$carga) {
                $errores[] = "Línea {$linea}: el grupo \"{$d['grupo_clave']}\" no tiene carga académica generada para \"{$d['materia_clave']}\". Genera primero la carga académica del grupo.";
                continue;
            }

            $profesorId = null;
            if (!empty($d['profesor_correo'])) {
                $profesor = Profesor::where('correo', $d['profesor_correo'])->first();
                if (!$profesor) {
                    $errores[] = "Línea {$linea}: no existe un profesor con correo \"{$d['profesor_correo']}\".";
                    continue;
                }
                $profesorId = $profesor->id;
            }

            if (!empty($d['horario']) && strlen($d['horario']) > 50) {
                $errores[] = "Línea {$linea}: el horario no puede tener más de 50 caracteres.";
                continue;
            }

            if (!empty($d['aula']) && strlen($d['aula']) > 100) {
                $errores[] = "Línea {$linea}: el aula no puede tener más de 100 caracteres.";
                continue;
            }

            $fechaInicioRaw = $d['fecha_inicio'] ?: null;
            $fechaFinRaw    = $d['fecha_fin'] ?: null;

            if (($fechaInicioRaw && !strtotime($fechaInicioRaw)) || ($fechaFinRaw && !strtotime($fechaFinRaw))) {
                $errores[] = "Línea {$linea}: fecha_inicio o fecha_fin no es una fecha válida.";
                continue;
            }

            // Normalizado a Y-m-d para que las comparaciones de traslape y el guardado sean consistentes
            // sin importar el formato de fecha que haya usado el coordinador en el CSV (ej. 10/01/2026).
            $fechaInicio = $fechaInicioRaw ? date('Y-m-d', strtotime($fechaInicioRaw)) : null;
            $fechaFin    = $fechaFinRaw ? date('Y-m-d', strtotime($fechaFinRaw)) : null;

            if ($fechaInicio && $fechaFin && $fechaFin < $fechaInicio) {
                $errores[] = "Línea {$linea}: fecha_fin no puede ser anterior a fecha_inicio.";
                continue;
            }

            if ($fechaInicio && $fechaFin) {
                $traslapeBd = CargaAcademica::where('grupo_id', $grupo->id)
                    ->where('id', '!=', $carga->id)
                    ->whereNotNull('fecha_inicio')
                    ->whereNotNull('fecha_fin')
                    ->where('fecha_inicio', '<=', $fechaFin)
                    ->where('fecha_fin', '>=', $fechaInicio)
                    ->with('materia')
                    ->first();

                if ($traslapeBd) {
                    $errores[] = "Línea {$linea}: las fechas se traslapan con \"{$traslapeBd->materia->nombre}\" en el mismo grupo (ya asignada en el sistema).";
                    continue;
                }

                $traslapeLote = collect($ventanasPorGrupo[$grupo->id] ?? [])->first(
                    fn($v) => $v['inicio'] <= $fechaFin && $v['fin'] >= $fechaInicio
                );

                if ($traslapeLote) {
                    $errores[] = "Línea {$linea}: las fechas se traslapan con la línea {$traslapeLote['linea']} del mismo archivo (grupo \"{$d['grupo_clave']}\").";
                    continue;
                }

                $ventanasPorGrupo[$grupo->id][] = ['inicio' => $fechaInicio, 'fin' => $fechaFin, 'linea' => $linea];
            }

            $validas[] = [
                'carga'        => $carga,
                'profesor_id'  => $profesorId,
                'horario'      => $d['horario'] ?: null,
                'aula'         => $d['aula'] ?: null,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin'    => $fechaFin,
            ];
        }

        if (empty($validas)) {
            return back()->withErrors(array_merge(['No se actualizó ninguna asignación.'], $errores));
        }

        foreach ($validas as $v) {
            $v['carga']->update([
                'profesor_id'  => $v['profesor_id'],
                'horario'      => $v['horario'],
                'aula'         => $v['aula'],
                'fecha_inicio' => $v['fecha_inicio'],
                'fecha_fin'    => $v['fecha_fin'],
            ]);
        }

        $respuesta = back()->with('success', 'Se actualizaron ' . count($validas) . ' asignación(es) correctamente.');

        if (!empty($errores)) {
            $respuesta = $respuesta->withErrors($errores);
        }

        return $respuesta;
    }
}
