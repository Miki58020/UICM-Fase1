<?php

namespace App\Http\Controllers;

use App\Mail\AspiranteAprobado;
use App\Mail\AspiranteRechazado;
use App\Mail\RegistroConfirmado;
use App\Models\Aspirante;
use App\Models\Periodo;
use App\Models\PeriodoPrograma;
use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Normalizer;

class AspiranteController extends Controller
{
    public function index()
    {
        $aspirantes = Aspirante::with('programa')->latest()->get();
        return view('admin.aspirantes.index', compact('aspirantes'));
    }

    public function show(Aspirante $aspirante)
    {
        $aspirante->load(['programa', 'pagos', 'alumno']);
        return view('admin.aspirantes.show', compact('aspirante'));
    }

    public function aprobar(Aspirante $aspirante)
    {
        if ($aspirante->estado !== 'pendiente') {
            abort(403, 'Este aspirante ya fue procesado.');
        }

        $aspirante->load('programa');

        $periodoObj = Periodo::where('nombre', $aspirante->generacion)->first();

        $pp = PeriodoPrograma::where('periodo_id', $periodoObj?->id)
            ->where('programa_id', $aspirante->programa_id)
            ->where('activo', true)
            ->first();

        if (!$pp) {
            return redirect()->back()->with(
                'error',
                "No se puede aprobar: el programa \"{$aspirante->programa->nombre}\" no está configurado como activo en el periodo \"{$aspirante->generacion}\". Agrégalo en Periodos → Carreras antes de continuar."
            );
        }

        $aspirante->update(['estado' => 'aprobado']);

        Mail::to($aspirante->email)->send(new AspiranteAprobado($aspirante));

        return redirect()->route('admin.aspirantes.show', $aspirante)
            ->with('success', "Expediente aprobado. El aspirante puede proceder con el pago. Folio: {$aspirante->folio}");
    }

    public function rechazar(Request $request, Aspirante $aspirante)
    {
        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        if ($aspirante->estado !== 'pendiente') {
            abort(403, 'Este aspirante ya fue procesado.');
        }

        $aspirante->update([
            'estado'        => 'rechazado',
            'observaciones' => $request->observaciones,
        ]);

        Mail::to($aspirante->email)->send(new AspiranteRechazado($aspirante));

        return redirect()->back()->with('success', 'Aspirante rechazado correctamente.');
    }

    public function resultado(Request $request)
    {
        $folio = $request->query('folio');

        if (!$folio) {
            return redirect()->route('aspirantes.seguimiento')
                ->withErrors(['folio' => 'Debes ingresar un folio.']);
        }

        $aspirante = Aspirante::with(['programa', 'pagos', 'alumno'])
            ->where('folio', strtoupper($folio))
            ->first();

        if (!$aspirante) {
            return redirect()->route('aspirantes.seguimiento')
                ->withErrors(['folio' => 'Folio no encontrado. Verifica e intenta de nuevo.']);
        }

        $pago = $aspirante->pagos->last();

        if ($aspirante->estado === 'pendiente') {
            $pasoActual = 0;
        } elseif ($aspirante->estado === 'rechazado') {
            $pasoActual = 1;
        } elseif ($aspirante->estado === 'aprobado' && !$pago) {
            $pasoActual = 2;
        } elseif ($aspirante->estado === 'aprobado' && $pago?->estado === 'pendiente') {
            $pasoActual = 3;
        } elseif ($aspirante->estado === 'aprobado' && $pago?->estado === 'aprobado' && !$aspirante->alumno?->user_id) {
            $pasoActual = 4;
        } else {
            $pasoActual = 5;
        }

        return view('aspirantes.resultado', compact('aspirante', 'pasoActual'));
    }

    public function create()
    {
        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $periodos = Periodo::where('estado', 'activo')->orderBy('fecha_inicio_registro')->get()
            ->map(function ($p) use ($meses) {
                $p->rango = $meses[(int) $p->fecha_inicio_registro->format('n')] . ' ' . $p->fecha_inicio_registro->format('Y')
                          . ' – '
                          . $meses[(int) $p->fecha_fin_registro->format('n')] . ' ' . $p->fecha_fin_registro->format('Y');
                return $p;
            });

        $relaciones = PeriodoPrograma::with('programa')
            ->whereIn('periodo_id', $periodos->pluck('id'))
            ->where('activo', true)
            ->get();

        // Programas únicos agrupados por nivel para renderizar el select
        $programasActivos = $relaciones->pluck('programa')->unique('id')->filter()->groupBy('nivel');

        // Mapa JSON: periodo_nombre -> [clave1, clave2, ...]
        $mapaPeriodoPrograma = $relaciones->groupBy('periodo_id')
            ->mapWithKeys(function ($items, $periodoId) use ($periodos) {
                $periodo = $periodos->firstWhere('id', $periodoId);
                return $periodo
                    ? [$periodo->nombre => $items->pluck('programa.clave')->filter()->values()]
                    : [];
            });

        // Mapa JSON inverso: programa_clave -> [periodo_nombre1, ...]
        $mapaProgramaPeriodo = $relaciones->groupBy(fn($r) => $r->programa->clave ?? '')
            ->filter(fn($items, $clave) => $clave !== '')
            ->mapWithKeys(function ($items, $clave) use ($periodos) {
                $periodoIds = $items->pluck('periodo_id')->unique()->values();
                $nombres = $periodos->whereIn('id', $periodoIds)->pluck('nombre')->values();
                return [$clave => $nombres];
            });

        return view('aspirantes.registro', compact('periodos', 'programasActivos', 'mapaPeriodoPrograma', 'mapaProgramaPeriodo'));
    }

    public function store(Request $request)
    {
        if (Periodo::where('estado', 'activo')->doesntExist()) {
            return back()->withInput()->withErrors([
                'generacion' => 'Las inscripciones no están abiertas en este momento. No es posible enviar el formulario.',
            ]);
        }

        // Se resuelve antes de validar para poder acotar la unicidad de curp/email por programa:
        // la misma persona puede aplicar a programas distintos (doble carrera), pero no duplicar
        // su aplicación al mismo programa.
        $programaSeleccionado = Programa::where('clave', $request->programa_academico)
            ->where('activo', true)
            ->first();

        $esDoctorado = $programaSeleccionado?->nivel === 'doctorado';

        $request->validate([
            'nombre'            => 'required|string|max:100',
            'apellido_paterno'  => 'required|string|max:100',
            'apellido_materno'  => 'nullable|string|max:100',
            'curp'              => [
                'required', 'string', 'size:18',
                \Illuminate\Validation\Rule::unique('aspirantes', 'curp')->where('programa_id', $programaSeleccionado?->id),
            ],
            'fecha_nacimiento'  => 'required|date|before:today',
            'telefono'          => 'required|digits:10',
            'email'             => [
                'required', 'email:rfc,filter',
                \Illuminate\Validation\Rule::unique('aspirantes', 'email')->where('programa_id', $programaSeleccionado?->id),
            ],
            'programa_academico'=> 'required|string',
            'generacion'        => 'required|string|exists:periodos,nombre',
            'acta_nacimiento'        => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'curp_doc'               => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identificacion'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'comprobante_domicilio'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto'                   => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'certificado'            => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'titulo'            => [
                $esDoctorado ? 'required' : 'nullable',
                'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120',
            ],
        ]);

        $programa = $programaSeleccionado ?? abort(404);

        // Validar que el programa está disponible en el periodo seleccionado
        $periodoObj = Periodo::where('nombre', $request->generacion)->first();
        $combinacionValida = PeriodoPrograma::where('periodo_id', $periodoObj?->id)
            ->where('programa_id', $programa->id)
            ->where('activo', true)
            ->exists();

        if (!$combinacionValida) {
            return back()->withInput()->withErrors([
                'programa_academico' => 'El programa seleccionado no está disponible en el periodo de inscripción elegido.',
            ]);
        }

        $curp   = strtoupper($request->curp);
        $nombre = $this->normalizarTexto($request->nombre);
        $apPat  = $this->normalizarTexto($request->apellido_paterno);
        $apMat  = $this->normalizarTexto($request->apellido_materno);
        $folio  = $this->generarFolio($request->generacion);

        $carpetaCertificado = match($programa->nivel) {
            'licenciatura' => 'documentos/certificados/bachillerato',
            default        => 'documentos/certificados/titulo_licenciatura',
        };

        $actaUrl                  = $request->file('acta_nacimiento')->store('documentos/actas_nacimiento', 'local');
        $curpDocUrl               = $request->file('curp_doc')->store('documentos/curp', 'local');
        $certificadoUrl           = $request->file('certificado')->store($carpetaCertificado, 'local');
        $identificacionUrl        = $request->file('identificacion')->store('documentos/identificaciones', 'local');
        $comprobanteDomicilioUrl  = $request->file('comprobante_domicilio')->store('documentos/comprobantes_domicilio', 'local');
        $fotoUrl                  = $request->file('foto')->store('documentos/fotografias', 'local');

        $tituloUrl = null;
        if ($request->hasFile('titulo')) {
            $tituloUrl = $request->file('titulo')->store('documentos/titulos/titulo_maestria', 'local');
        }

        Aspirante::create([
            'folio'              => $folio,
            'nombre'             => $nombre,
            'apellido_paterno'   => $apPat,
            'apellido_materno'   => $apMat,
            'curp'               => $curp,
            'fecha_nacimiento'   => $request->fecha_nacimiento,
            'telefono'           => $request->telefono,
            'email'              => $request->email,
            'programa_id'        => $programa->id,
            'generacion'         => $request->generacion,
            'acta_nacimiento_url'       => $actaUrl,
            'curp_url'                  => $curpDocUrl,
            'certificado_url'           => $certificadoUrl,
            'identificacion_url'        => $identificacionUrl,
            'comprobante_domicilio_url' => $comprobanteDomicilioUrl,
            'foto_url'                  => $fotoUrl,
            'titulo_url'                => $tituloUrl,
            'estado'             => 'pendiente',
        ]);

        $aspirante = Aspirante::where('folio', $folio)->first();
        Mail::to($aspirante->email)->send(new RegistroConfirmado($aspirante));

        return redirect()->route('aspirantes.confirmacion')->with('folio', $folio);
    }

    private function generarFolio(string $generacion): string
    {
        $periodo = Periodo::where('nombre', $generacion)->first();
        $year = $periodo ? $periodo->fecha_inicio_registro->year : now()->year;
        $prefix = 'UICM-' . $year . '-';
        $count = Aspirante::where('folio', 'like', $prefix . '%')->count() + 1;
        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private function normalizarTexto(?string $texto): ?string
    {
        if ($texto === null) return null;
        // Descomponer caracteres acentuados y eliminar marcas diacríticas
        $texto = normalizer_normalize($texto, Normalizer::FORM_D);
        $texto = preg_replace('/\p{Mn}/u', '', $texto);
        return strtoupper($texto);
    }
}
