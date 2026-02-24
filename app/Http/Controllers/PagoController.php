<?php

namespace App\Http\Controllers;

use App\Models\Aspirante;
use App\Models\Pago;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class PagoController extends Controller
{
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
