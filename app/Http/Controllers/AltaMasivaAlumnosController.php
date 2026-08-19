<?php

namespace App\Http\Controllers;

use App\Mail\BienvenidaAlumno;
use App\Models\Alumno;
use App\Models\CargaAcademica;
use App\Models\ConfiguracionCorreo;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Programa;
use App\Models\User;
use App\Support\Texto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Support\Contrasena;

class AltaMasivaAlumnosController extends Controller
{
    public function index()
    {
        $programas = Programa::where('activo', true)->orderBy('nombre')->get();
        $periodos  = Periodo::orderByDesc('fecha_inicio_registro')->get();
        $grupos    = Grupo::with('programa', 'periodo')->orderBy('clave')->get();

        return view('admin.alta-masiva-alumnos.index', compact('programas', 'periodos', 'grupos'));
    }

    public function plantillaMigracion()
    {
        $columnas = [
            'nombre', 'apellido_paterno', 'apellido_materno', 'email',
            'curp', 'telefono', 'fecha_nacimiento',
        ];

        // En mayúsculas y sin acentos: así es como el sistema termina guardando el
        // nombre de todas formas (ver Texto::normalizarNombre), para que el ejemplo
        // no sugiera un formato que luego se transforma.
        $ejemplo = [
            'JUAN', 'PEREZ', 'GARCIA', 'juan.perez@example.com',
            'PEGJ900101HDFRRN09', '5512345678', '1990-01-01',
        ];

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
            'Content-Disposition' => 'attachment; filename="plantilla_migracion_alumnos.csv"',
        ]);
    }

    public function importarMigracion(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'csv'      => 'required|file|mimes:csv,txt',
        ]);

        $grupoDestino = Grupo::with('programa', 'periodo')->findOrFail($request->grupo_id);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');
        // Si el archivo trae BOM UTF-8 (ej. exportado con Excel o con nuestra propia
        // plantilla), se descarta para que el primer encabezado no quede como "﻿nombre".
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

            // Acotado por programa: la misma persona puede estar migrada en más de un
            // programa (doble carrera) con el mismo correo personal de contacto.
            if (Alumno::where('email', $d['email'])->where('programa_id', $grupoDestino->programa_id)->exists()) {
                $errores[] = "Línea {$fila['linea']}: ya existe un alumno con el correo \"{$d['email']}\" en este programa.";
                continue;
            }

            // Mismo criterio que el registro de aspirantes: nombre/apellidos en
            // mayúsculas y sin acentos, sin importar cómo vengan en el CSV.
            $d['nombre']           = Texto::normalizarNombre($d['nombre']);
            $d['apellido_paterno'] = Texto::normalizarNombre($d['apellido_paterno']);
            $d['apellido_materno'] = Texto::normalizarNombre($d['apellido_materno']);

            // Los límites de las columnas los aplica el motor de forma estricta: sin
            // estas comprobaciones una sola fila larga aborta la importación completa
            // en lugar de reportarse junto con los demás errores.
            $limites = [
                'nombre'           => 255,
                'apellido_paterno' => 255,
                'apellido_materno' => 255,
                'email'            => 255,
                'curp'             => 18,
                'telefono'         => 20,
            ];

            $excedido = null;
            foreach ($limites as $campo => $maximo) {
                if (mb_strlen((string) ($d[$campo] ?? '')) > $maximo) {
                    $excedido = "Línea {$fila['linea']}: {$campo} no puede tener más de {$maximo} caracteres.";
                    break;
                }
            }

            if ($excedido !== null) {
                $errores[] = $excedido;
                continue;
            }

            // fecha_nacimiento es una columna DATE: un valor que no se pueda interpretar
            // rompe la inserción, así que se valida y se normaliza a Y-m-d.
            if (!empty($d['fecha_nacimiento'])) {
                $fecha = $this->interpretarFecha($d['fecha_nacimiento']);
                if ($fecha === null) {
                    $errores[] = "Línea {$fila['linea']}: \"{$d['fecha_nacimiento']}\" no es una fecha de nacimiento válida (usa el formato AAAA-MM-DD).";
                    continue;
                }
                $d['fecha_nacimiento'] = $fecha;
            }

            $validas[] = $d;
        }

        if (empty($validas)) {
            return back()->withErrors(array_merge(['No se importó ningún alumno.'], $errores));
        }

        // El límite por defecto de PHP (30s) no alcanza si varias filas fallan al enviar
        // correo: cada una puede consumir hasta 15s de reintento. Se amplía según el
        // tamaño del lote para que el reintento nunca corte la importación a medias.
        set_time_limit(60 + count($validas) * 35);

        $grupoActual = $grupoDestino;
        $grupoActual->loadCount('alumnos');
        $espacioDisponible = $grupoActual->capacidad - $grupoActual->alumnos_count;
        $gruposCreados = [];
        $creados = 0;
        $dominio = ConfiguracionCorreo::dominio();

        foreach ($validas as $d) {
            if ($espacioDisponible <= 0) {
                $grupoActual = $this->crearGrupoHermano($grupoDestino);
                $this->generarCargaAcademicaParaGrupo($grupoActual);
                $gruposCreados[] = $grupoActual->clave;
                $espacioDisponible = $grupoActual->capacidad;
            }

            $matricula = Alumno::generarMatricula($grupoDestino->periodo, $grupoDestino->programa);
            $password = Contrasena::generar();

            $user = User::create([
                'name'     => trim("{$d['nombre']} {$d['apellido_paterno']} {$d['apellido_materno']}"),
                'email'    => strtolower($matricula) . '@' . $dominio,
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

            $intentos = 0;
            while ($intentos < 2) {
                try {
                    Mail::to($alumno->email)->send(new BienvenidaAlumno($alumno, $password));
                    break;
                } catch (\Throwable $e) {
                    $intentos++;
                    if ($intentos < 2) {
                        sleep(15);
                    } else {
                        $errores[] = "{$alumno->nombre_completo} ({$alumno->matricula}) se creó correctamente, pero no se pudo enviar el correo de bienvenida.";
                    }
                }
            }

            $espacioDisponible--;
            $creados++;
        }

        $mensaje = "Se importaron {$creados} alumno(s) correctamente.";
        if (!empty($gruposCreados)) {
            $mensaje .= ' Grupos creados: ' . implode(', ', $gruposCreados) . '.';
        }

        $respuesta = back()->with('success', $mensaje);

        if (!empty($errores)) {
            $respuesta = $respuesta->withErrors($errores);
        }

        return $respuesta;
    }

    /**
     * Convierte la fecha del CSV a Y-m-d, o devuelve null si no es válida.
     *
     * Se interpretan los formatos de forma explícita en lugar de usar strtotime():
     * esa función lee "01/03/1995" como mes/día/año y guardaría el 3 de enero en vez
     * del 1 de marzo, sin marcar ningún error. La comparación final contra el valor
     * original descarta además fechas inexistentes como 31/02, que PHP ajustaría solo.
     */
    private function interpretarFecha(string $valor): ?string
    {
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $formato) {
            $fecha = \DateTime::createFromFormat('!' . $formato, $valor);

            if ($fecha !== false && $fecha->format($formato) === $valor) {
                return $fecha->format('Y-m-d');
            }
        }

        return null;
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

    private function generarCargaAcademicaParaGrupo(Grupo $grupo): void
    {
        $materias = Materia::where('programa_id', $grupo->programa_id)
            ->where('cuatrimestre', $grupo->cuatrimestre)
            ->where('activo', true)
            ->get();

        foreach ($materias as $materia) {
            CargaAcademica::firstOrCreate(
                ['grupo_id' => $grupo->id, 'materia_id' => $materia->id, 'periodo_id' => $grupo->periodo_id],
                ['profesor_id' => null, 'dia_semana' => null, 'hora_inicio' => null, 'hora_fin' => null, 'aula' => null, 'estado_revision' => null]
            );
        }
    }
}
