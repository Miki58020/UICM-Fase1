<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession as BaseStartSession;

class StartSession extends BaseStartSession
{
    /**
     * Rutas que sirven archivos/imágenes y se cargan como recursos (<img>, etc.)
     * dentro de otras páginas. Laravel las trata como navegación GET normal y
     * pisaría la "URL anterior" de la sesión, rompiendo cualquier redirect()->back().
     */
    protected const RUTAS_SIN_TRACKING = [
        'admin.archivo',
    ];

    protected function storeCurrentUrl(Request $request, $session)
    {
        if (in_array($request->route()?->getName(), static::RUTAS_SIN_TRACKING, true)) {
            return;
        }

        parent::storeCurrentUrl($request, $session);
    }
}
