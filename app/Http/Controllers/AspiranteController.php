<?php

namespace App\Http\Controllers;

use App\Mail\AspiranteAprobado;
use App\Mail\AspiranteRechazado;
use App\Mail\RegistroConfirmado;
use App\Models\Alumno;
use App\Models\Aspirante;
use App\Models\Programa;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
        $aspirante->load('programa');
        return view('admin.aspirantes.show', compact('aspirante'));
    }

    public function aprobar(Aspirante $aspirante)
    {
        if ($aspirante->estado !== 'pendiente') {
            abort(403, 'Este aspirante ya fue procesado.');
        }

        $matricula = $this->generarMatricula();

        Alumno::create([
            'matricula'          => $matricula,
            'aspirante_id'       => $aspirante->id,
            'programa_id'        => $aspirante->programa_id,
            'nombre'             => $aspirante->nombre,
            'apellido_paterno'   => $aspirante->apellido_paterno,
            'apellido_materno'   => $aspirante->apellido_materno,
            'email'              => $aspirante->email,
            'cuatrimestre_actual'=> 1,
            'estado'             => 'activo',
        ]);

        $aspirante->update(['estado' => 'aprobado']);

        Mail::to($aspirante->email)->send(new AspiranteAprobado($aspirante));

        return redirect()->back()->with('success', "Aspirante aprobado. Matrícula generada: {$matricula}");
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

        $aspirante = Aspirante::with(['programa', 'pagos'])
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
        } elseif ($aspirante->estado === 'aprobado' && $pago && $pago->estado === 'pendiente') {
            $pasoActual = 3;
        } else {
            $pasoActual = 4;
        }

        return view('aspirantes.resultado', compact('aspirante', 'pasoActual'));
    }

    public function create()
    {
        return view('aspirantes.registro');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'            => 'required|string|max:100',
            'apellido_paterno'  => 'required|string|max:100',
            'apellido_materno'  => 'nullable|string|max:100',
            'curp'              => 'required|string|size:18|unique:aspirantes,curp',
            'fecha_nacimiento'  => 'required|date|before:today',
            'telefono'          => 'required|digits:10',
            'email'             => 'required|email:rfc,filter|unique:aspirantes,email',
            'programa_academico'=> 'required|string',
            'generacion'        => 'required|string|max:10',
            'acta_nacimiento'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificado'       => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identificacion'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $programa = Programa::where('clave', $request->programa_academico)
            ->where('activo', true)
            ->firstOrFail();

        $curp   = strtoupper($request->curp);
        $nombre = $this->normalizarTexto($request->nombre);
        $apPat  = $this->normalizarTexto($request->apellido_paterno);
        $apMat  = $this->normalizarTexto($request->apellido_materno);
        $folio  = $this->generarFolio();

        $actaUrl = Cloudinary::uploadApi()->upload(
            $request->file('acta_nacimiento')->getRealPath(),
            ['folder' => 'uicm/documentos', 'public_id' => $curp . '_acta_' . time(), 'resource_type' => 'auto']
        )['secure_url'];

        $certificadoUrl = Cloudinary::uploadApi()->upload(
            $request->file('certificado')->getRealPath(),
            ['folder' => 'uicm/documentos', 'public_id' => $curp . '_cert_' . time(), 'resource_type' => 'auto']
        )['secure_url'];

        $identificacionUrl = Cloudinary::uploadApi()->upload(
            $request->file('identificacion')->getRealPath(),
            ['folder' => 'uicm/documentos', 'public_id' => $curp . '_id_' . time(), 'resource_type' => 'auto']
        )['secure_url'];

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
            'acta_nacimiento_url'  => $actaUrl,
            'certificado_url'      => $certificadoUrl,
            'identificacion_url'   => $identificacionUrl,
            'estado'             => 'pendiente',
        ]);

        $aspirante = Aspirante::where('folio', $folio)->first();
        Mail::to($aspirante->email)->send(new RegistroConfirmado($aspirante));

        return redirect()->route('aspirantes.confirmacion')->with('folio', $folio);
    }

    private function generarFolio(): string
    {
        $year = now()->year;
        $count = Aspirante::whereYear('created_at', $year)->count() + 1;
        return 'UICM-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private function generarMatricula(): string
    {
        $year = now()->year;
        $count = Alumno::whereYear('created_at', $year)->count() + 1;
        return 'UICM-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
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
