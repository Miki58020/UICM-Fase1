<?php

namespace App\Http\Controllers;

use App\Models\Aspirante;
use App\Models\Programa;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class AspiranteController extends Controller
{
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

        $curp = strtoupper($request->curp);
        $folio = $this->generarFolio();

        $actaUrl = Cloudinary::upload(
            $request->file('acta_nacimiento')->getRealPath(),
            ['folder' => 'uicm/documentos', 'public_id' => $curp . '_acta_' . time(), 'resource_type' => 'auto']
        )->getSecurePath();

        $certificadoUrl = Cloudinary::upload(
            $request->file('certificado')->getRealPath(),
            ['folder' => 'uicm/documentos', 'public_id' => $curp . '_cert_' . time(), 'resource_type' => 'auto']
        )->getSecurePath();

        $identificacionUrl = Cloudinary::upload(
            $request->file('identificacion')->getRealPath(),
            ['folder' => 'uicm/documentos', 'public_id' => $curp . '_id_' . time(), 'resource_type' => 'auto']
        )->getSecurePath();

        Aspirante::create([
            'folio'              => $folio,
            'nombre'             => $request->nombre,
            'apellido_paterno'   => $request->apellido_paterno,
            'apellido_materno'   => $request->apellido_materno,
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

        return redirect()->route('aspirantes.confirmacion')->with('folio', $folio);
    }

    private function generarFolio(): string
    {
        $year = now()->year;
        $count = Aspirante::whereYear('created_at', $year)->count() + 1;
        return 'UICM-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
