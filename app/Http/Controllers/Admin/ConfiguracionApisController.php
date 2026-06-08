<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionCorreo;
use App\Models\ConfiguracionMercadopago;
use Illuminate\Http\Request;

class ConfiguracionApisController extends Controller
{
    public function index(Request $request)
    {
        $configMercadopago = ConfiguracionMercadopago::activa() ?? ConfiguracionMercadopago::latest()->first();
        $configCorreo      = ConfiguracionCorreo::activa() ?? ConfiguracionCorreo::latest()->first();

        $tab = in_array($request->query('tab'), ['mercadopago', 'correo'])
            ? $request->query('tab')
            : 'mercadopago';

        return view('admin.apis.index', [
            'configMercadopago' => $configMercadopago,
            'configCorreo'      => $configCorreo,
            'tabInicial'        => $tab,
        ]);
    }
}
