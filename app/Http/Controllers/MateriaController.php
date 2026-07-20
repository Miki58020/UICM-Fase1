<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Programa;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index()
    {
        $materias  = Materia::with('programa')
            ->orderBy('programa_id')
            ->orderBy('cuatrimestre')
            ->get();

        $programas = Programa::where('activo', true)->orderBy('nombre')->get();

        return view('admin.materias.index', compact('materias', 'programas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clave'        => 'required|string|max:20|unique:materias,clave',
            'nombre'       => 'required|string|max:100',
            'programa_id'  => 'required|exists:programas,id',
            'cuatrimestre' => ['required', 'integer', 'min:1', $this->reglaCuatrimestreValido($request)],
            'creditos'     => 'required|integer|min:1|max:20',
        ]);

        Materia::create([
            'clave'        => strtoupper($request->clave),
            'nombre'       => $request->nombre,
            'programa_id'  => $request->programa_id,
            'cuatrimestre' => $request->cuatrimestre,
            'creditos'     => $request->creditos,
            'activo'       => true,
        ]);

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia registrada correctamente.');
    }

    public function update(Request $request, Materia $materia)
    {
        $request->validate([
            'clave'        => 'required|string|max:20|unique:materias,clave,' . $materia->id,
            'nombre'       => 'required|string|max:100',
            'programa_id'  => 'required|exists:programas,id',
            'cuatrimestre' => ['required', 'integer', 'min:1', $this->reglaCuatrimestreValido($request)],
            'creditos'     => 'required|integer|min:1|max:20',
        ]);

        $materia->update([
            'clave'        => strtoupper($request->clave),
            'nombre'       => $request->nombre,
            'programa_id'  => $request->programa_id,
            'cuatrimestre' => $request->cuatrimestre,
            'creditos'     => $request->creditos,
        ]);

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia actualizada correctamente.');
    }

    public function toggle(Materia $materia)
    {
        $materia->update(['activo' => !$materia->activo]);

        return redirect()->back()
            ->with('success', 'Estado de la materia actualizado.');
    }

    public function plantillaMaterias()
    {
        $columnas = ['clave', 'nombre', 'programa_clave', 'cuatrimestre', 'creditos'];

        $ejemplo = ['IS-101', 'Programación I', 'administracion', '1', '8'];

        $filas = [$columnas, $ejemplo];

        $callback = function () use ($filas) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM para acentos en Excel
            foreach ($filas as $fila) {
                fputcsv($handle, $fila, escape: '');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_materias.csv"',
        ]);
    }

    public function importarMaterias(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');
        // Si el archivo trae BOM UTF-8 (ej. exportado con Excel o con nuestra propia
        // plantilla), se descarta para que el primer encabezado no quede como "﻿clave".
        if (fread($handle, 3) !== "\xEF\xBB\xBF") {
            rewind($handle);
        }
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
        $creadas = 0;
        $actualizadas = 0;

        foreach ($filas as $fila) {
            $d = $fila['datos'];
            $linea = $fila['linea'];

            $faltantes = array_filter(
                ['clave', 'nombre', 'programa_clave', 'cuatrimestre', 'creditos'],
                fn($campo) => empty($d[$campo] ?? null)
            );
            if (!empty($faltantes)) {
                $errores[] = "Línea {$linea}: faltan los campos (" . implode(', ', $faltantes) . ").";
                continue;
            }

            $programa = Programa::where('clave', $d['programa_clave'])->first();
            if (!$programa) {
                $errores[] = "Línea {$linea}: no existe el programa \"{$d['programa_clave']}\".";
                continue;
            }

            if (!ctype_digit($d['cuatrimestre']) || (int) $d['cuatrimestre'] < 1) {
                $errores[] = "Línea {$linea}: cuatrimestre debe ser un número entero mayor o igual a 1.";
                continue;
            }
            $cuatrimestre = (int) $d['cuatrimestre'];
            if ($cuatrimestre > $programa->duracion_cuatrimestres) {
                $errores[] = "Línea {$linea}: el programa \"{$d['programa_clave']}\" dura {$programa->duracion_cuatrimestres} cuatrimestres.";
                continue;
            }

            if (!ctype_digit($d['creditos']) || (int) $d['creditos'] < 1 || (int) $d['creditos'] > 20) {
                $errores[] = "Línea {$linea}: creditos debe ser un número entero entre 1 y 20.";
                continue;
            }

            $clave = strtoupper($d['clave']);
            if (strlen($clave) > 20) {
                $errores[] = "Línea {$linea}: la clave no puede tener más de 20 caracteres.";
                continue;
            }

            $existente = Materia::where('clave', $clave)->first();

            Materia::updateOrCreate(
                ['clave' => $clave],
                [
                    'nombre'       => $d['nombre'],
                    'programa_id'  => $programa->id,
                    'cuatrimestre' => $cuatrimestre,
                    'creditos'     => (int) $d['creditos'],
                    'activo'       => $existente->activo ?? true,
                ]
            );

            $existente ? $actualizadas++ : $creadas++;
        }

        if ($creadas === 0 && $actualizadas === 0) {
            return back()->withErrors(array_merge(['No se procesó ninguna materia.'], $errores));
        }

        $resumen = "Materias creadas: {$creadas}. Actualizadas: {$actualizadas}.";

        if (!empty($errores)) {
            return back()->withErrors(array_merge([$resumen], $errores));
        }

        return redirect()->route('admin.materias.index')->with('success', $resumen);
    }

    private function reglaCuatrimestreValido(Request $request)
    {
        return function (string $attribute, $value, \Closure $fail) use ($request) {
            $programa = Programa::find($request->programa_id);
            if ($programa && (int) $value > $programa->duracion_cuatrimestres) {
                $fail("El programa seleccionado dura {$programa->duracion_cuatrimestres} cuatrimestres.");
            }
        };
    }
}
