<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionCorreo;
use Illuminate\Http\Request;

class ConfiguracionCorreoController extends Controller
{
    public function updateConexion(Request $request, ConfiguracionCorreo $configuracion)
    {
        $request->validate([
            'mailer'   => 'required|in:smtp,sendmail,log',
            'host'     => 'required_if:mailer,smtp|nullable|string',
            'port'     => 'required_if:mailer,smtp|nullable|integer|min:1|max:65535',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
        ]);

        // El password guardado puede no ser descifrable con el APP_KEY actual
        // (p. ej. si se cifró con una clave distinta). Se limpia el valor original
        // antes de actualizar para que Eloquent no intente descifrarlo al comparar.
        $configuracion->setRawAttributes(
            array_merge($configuracion->getAttributes(), ['password' => null]),
            true
        );

        $configuracion->update($request->only(['mailer', 'host', 'port', 'username', 'password']));

        return redirect()->route('admin.apis.index', ['tab' => 'correo'])
            ->with('success', 'Conexión de correo actualizada correctamente.');
    }

    public function updateRemitente(Request $request, ConfiguracionCorreo $configuracion)
    {
        $request->validate([
            'from_address' => 'required|email',
            'from_name'    => 'required|string|max:255',
        ]);

        $configuracion->update($request->only(['from_address', 'from_name']));

        return redirect()->route('admin.apis.index', ['tab' => 'correo'])
            ->with('success', 'Remitente actualizado correctamente.');
    }
}
