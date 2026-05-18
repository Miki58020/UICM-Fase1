<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionMercadopago;
use Illuminate\Http\Request;

class ConfiguracionMercadopagoController extends Controller
{
    public function index()
    {
        $config = ConfiguracionMercadopago::activa() ?? ConfiguracionMercadopago::latest()->first();
        return view('admin.mercadopago.index', compact('config'));
    }

    public function updateCredenciales(Request $request, ConfiguracionMercadopago $configuracion)
    {
        $request->validate([
            'public_key'   => 'required|string',
            'access_token' => 'required|string',
        ]);

        $configuracion->update($request->only(['public_key', 'access_token']));

        return redirect()->route('admin.mercadopago.index')
            ->with('success', 'Credenciales actualizadas correctamente.');
    }

    public function updateUrls(Request $request, ConfiguracionMercadopago $configuracion)
    {
        $request->validate([
            'back_url_success' => 'required|url',
            'back_url_pending' => 'required|url',
            'back_url_failure' => 'required|url',
            'notification_url' => 'nullable|url',
        ]);

        $configuracion->update($request->only([
            'back_url_success',
            'back_url_pending',
            'back_url_failure',
            'notification_url',
        ]));

        return redirect()->route('admin.mercadopago.index')
            ->with('success', 'URLs de retorno actualizadas correctamente.');
    }
}
