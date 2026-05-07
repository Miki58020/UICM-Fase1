<?php

namespace App\Http\Controllers;

use App\Models\CarruselImagen;
use App\Models\ConfiguracionSitio;
use App\Models\ContactoInteres;
use App\Models\OfertaPrograma;
use App\Models\Periodo;

class HomeController extends Controller
{
    public function index()
    {
        $periodoActivo = Periodo::where('estado', 'activo')->first();
        $imagenes      = CarruselImagen::where('activo', true)->orderBy('orden')->get();
        $ofertaHome    = OfertaPrograma::where('activo', true)->orderBy('orden')->get()->groupBy('nivel');
        $contacto      = [
            'correo'   => ConfiguracionSitio::get('correo',   'contacto@uicm.edu.mx'),
            'telefono' => ConfiguracionSitio::get('telefono', '(55) 0000 0000'),
            'horario'  => ConfiguracionSitio::get('horario',  'Lunes a viernes de 9:00 a 18:00 hrs.'),
        ];
        $conteoProgramas = OfertaPrograma::where('activo', true)
            ->selectRaw('nivel, count(*) as total')
            ->groupBy('nivel')
            ->pluck('total', 'nivel');

        $interesesContacto = ContactoInteres::where('activo', true)->orderBy('orden')->get();

        return view('home.index', compact('periodoActivo', 'imagenes', 'ofertaHome', 'contacto', 'conteoProgramas', 'interesesContacto'));
    }

    public function ofertaEducativa()
    {
        $programas = OfertaPrograma::where('activo', true)->orderBy('orden')->get()->groupBy('nivel');
        $contacto  = [
            'correo'   => ConfiguracionSitio::get('correo',   'contacto@uicm.edu.mx'),
            'telefono' => ConfiguracionSitio::get('telefono', '(55) 0000 0000'),
            'horario'  => ConfiguracionSitio::get('horario',  'Lunes a viernes de 9:00 a 18:00 hrs.'),
        ];

        return view('home.oferta-educativa', compact('programas', 'contacto'));
    }
}
