<?php

namespace App\Http\Controllers;

use App\Mail\PagoAprobado;
use App\Mail\PagoRechazado;
use App\Models\Aspirante;
use App\Models\Pago;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PagoController extends Controller
{
    public function create(Request $request)
    {
        $folio = strtoupper($request->query('folio', ''));

        if (!$folio) {
            return redirect()->route('aspirantes.seguimiento')
                ->withErrors(['folio' => 'Debes consultar tu folio primero.']);
        }

        $aspirante = Aspirante::with('programa')
            ->where('folio', $folio)
            ->where('estado', 'aprobado')
            ->first();

        if (!$aspirante) {
            return redirect()->route('aspirantes.seguimiento')
                ->withErrors(['folio' => 'Folio no encontrado o solicitud aún no aprobada.']);
        }

        return view('aspirantes.pago', compact('aspirante'));
    }

    public function index()
    {
        $pagos = Pago::with('aspirante.programa')->latest()->get();
        return view('finanzas.pagos.index', compact('pagos'));
    }

    public function show(Pago $pago)
    {
        $pago->load('aspirante.programa');
        return view('finanzas.pagos.show', compact('pago'));
    }

    public function aprobar(Pago $pago)
    {
        if ($pago->estado !== 'pendiente') {
            abort(403, 'Este pago ya fue procesado.');
        }

        $pago->update(['estado' => 'aprobado']);

        Mail::to($pago->aspirante->email)->send(new PagoAprobado($pago));

        return redirect()->back()->with('success', 'Pago aprobado correctamente.');
    }

    public function rechazar(Request $request, Pago $pago)
    {
        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        if ($pago->estado !== 'pendiente') {
            abort(403, 'Este pago ya fue procesado.');
        }

        $pago->update([
            'estado'        => 'rechazado',
            'observaciones' => $request->observaciones,
        ]);

        Mail::to($pago->aspirante->email)->send(new PagoRechazado($pago));

        return redirect()->back()->with('success', 'Pago rechazado correctamente.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'folio'       => ['required', 'string'],
            'comprobante' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'comprobante.required' => 'Debes subir el comprobante de pago.',
            'comprobante.mimes'    => 'El archivo debe ser PDF, JPG o PNG.',
            'comprobante.max'      => 'El archivo no debe superar los 5 MB.',
        ]);

        // Subir comprobante a Cloudinary
        $resultado = Cloudinary::upload($request->file('comprobante')->getRealPath(), [
            'folder'          => 'uicm/comprobantes',
            'public_id'       => 'pago_' . $request->folio . '_' . time(),
            'resource_type'   => 'auto',
        ]);

        $urlComprobante = $resultado->getSecurePath();

        // Buscar aspirante por folio
        $aspirante = Aspirante::where('folio', $request->folio)->first();

        // Registrar pago en BD
        Pago::create([
            'aspirante_id' => $aspirante?->id,
            'concepto'     => 'inscripcion',
            'periodo'      => date('Y') . '-1',
            'monto'        => 3500.00,
            'comprobante'  => $urlComprobante,
            'fecha_pago'   => now()->toDateString(),
            'estado'       => 'pendiente',
        ]);

        return redirect()->route('aspirantes.pago.confirmacion')
                         ->with('comprobante_url', $urlComprobante);
    }
}
