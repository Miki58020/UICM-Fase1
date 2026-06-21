<?php

namespace App\Http\Controllers;

use App\Mail\BienvenidaAlumno;
use App\Models\Alumno;
use App\Models\CargaAcademica;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Profesor;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            ->with('carga_success', "Carga académica generada para el grupo {$grupo->clave}.");
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
                ['profesor_id' => null, 'horario' => null, 'aula' => null]
            );
        }
    }

    public function plantillaMigracion()
    {
        $columnas = [
            'nombre', 'apellido_paterno', 'apellido_materno', 'email',
            'curp', 'telefono', 'fecha_nacimiento',
        ];

        $ejemplo = [
            'Juan', 'Pérez', 'García', 'juan.perez@example.com',
            'PEGJ900101HDFRRN09', '5512345678', '1990-01-01',
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
            'Content-Disposition' => 'attachment; filename="plantilla_migracion_alumnos.csv"',
        ]);
    }

    public function actualizar(Request $request, CargaAcademica $carga)
    {
        $request->validate([
            'profesor_id'  => 'nullable|exists:profesores,id',
            'horario'      => 'nullable|string|max:50',
            'aula'         => 'nullable|string|max:20',
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

    public function importarMigracion(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'csv'      => 'required|file|mimes:csv,txt',
        ]);

        $grupoDestino = Grupo::with('programa', 'periodo')->findOrFail($request->grupo_id);

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

        foreach ($filas as $fila) {
            $d = $fila['datos'];
            $faltantes = array_filter(
                ['nombre', 'apellido_paterno', 'apellido_materno', 'email'],
                fn($campo) => empty($d[$campo] ?? null)
            );

            if (!empty($faltantes)) {
                $errores[] = "Línea {$fila['linea']}: faltan los campos (" . implode(', ', $faltantes) . ").";
                continue;
            }

            if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = "Línea {$fila['linea']}: el correo \"{$d['email']}\" no es válido.";
                continue;
            }

            if (Alumno::where('email', $d['email'])->exists()) {
                $errores[] = "Línea {$fila['linea']}: ya existe un alumno con el correo \"{$d['email']}\".";
                continue;
            }

            $validas[] = $d;
        }

        if (empty($validas)) {
            return back()->withErrors(array_merge(['No se importó ningún alumno.'], $errores));
        }

        $grupoActual = $grupoDestino;
        $grupoActual->loadCount('alumnos');
        $espacioDisponible = $grupoActual->capacidad - $grupoActual->alumnos_count;
        $gruposCreados = [];
        $creados = 0;

        foreach ($validas as $d) {
            if ($espacioDisponible <= 0) {
                $grupoActual = $this->crearGrupoHermano($grupoDestino);
                $this->generarCargaAcademicaParaGrupo($grupoActual);
                $gruposCreados[] = $grupoActual->clave;
                $espacioDisponible = $grupoActual->capacidad;
            }

            $matricula = Alumno::generarMatricula($grupoDestino->periodo, $grupoDestino->programa);
            $password = Str::random(8);

            $user = User::create([
                'name'     => trim("{$d['nombre']} {$d['apellido_paterno']} {$d['apellido_materno']}"),
                'email'    => strtolower($matricula) . '@uicm.edu.mx',
                'password' => Hash::make($password),
                'rol'      => 'alumno',
            ]);

            $alumno = Alumno::create([
                'matricula'           => $matricula,
                'user_id'             => $user->id,
                'programa_id'         => $grupoDestino->programa_id,
                'periodo_id'          => $grupoDestino->periodo_id,
                'grupo_id'            => $grupoActual->id,
                'nombre'              => $d['nombre'],
                'apellido_paterno'    => $d['apellido_paterno'],
                'apellido_materno'    => $d['apellido_materno'],
                'email'               => $d['email'],
                'curp'                => $d['curp'] ?? null,
                'telefono'            => $d['telefono'] ?? null,
                'fecha_nacimiento'    => $d['fecha_nacimiento'] ?: null,
                'cuatrimestre_actual' => $grupoDestino->cuatrimestre,
                'creditos_acumulados' => 0,
                'estado'              => 'activo',
                'migrado'             => true,
            ]);

            $alumno->load('programa', 'grupo');

            try {
                Mail::to($alumno->email)->send(new BienvenidaAlumno($alumno, $password));
            } catch (\Throwable $e) {
                $errores[] = "{$alumno->nombre_completo} ({$alumno->matricula}) se creó correctamente, pero no se pudo enviar el correo de bienvenida.";
            }

            $espacioDisponible--;
            $creados++;
        }

        return redirect()
            ->route('admin.carga-academica.index', ['grupo_id' => $grupoDestino->id])
            ->with('success', "Se importaron {$creados} alumno(s) correctamente.")
            ->with('migracion_resultado', [
                'creados'       => $creados,
                'gruposCreados' => $gruposCreados,
                'errores'       => $errores,
            ]);
    }

    private function crearGrupoHermano(Grupo $referencia): Grupo
    {
        $base = preg_replace('/-[A-Z]$/', '', $referencia->clave);

        foreach (range('A', 'Z') as $letra) {
            $clave = "{$base}-{$letra}";
            if (!Grupo::where('clave', $clave)->exists()) {
                return Grupo::create([
                    'clave'       => $clave,
                    'programa_id' => $referencia->programa_id,
                    'periodo_id'  => $referencia->periodo_id,
                    'cuatrimestre' => $referencia->cuatrimestre,
                    'capacidad'   => $referencia->capacidad,
                ]);
            }
        }

        $n = 1;
        do {
            $clave = "{$base}-G{$n}";
            $n++;
        } while (Grupo::where('clave', $clave)->exists());

        return Grupo::create([
            'clave'        => $clave,
            'programa_id'  => $referencia->programa_id,
            'periodo_id'   => $referencia->periodo_id,
            'cuatrimestre' => $referencia->cuatrimestre,
            'capacidad'    => $referencia->capacidad,
        ]);
    }
}
