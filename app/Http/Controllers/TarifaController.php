<?php

namespace App\Http\Controllers;

use App\Models\TarifaInscripcion;
use Illuminate\Http\Request;

class TarifaController extends Controller
{
    public function index()
    {
        $tarifas = TarifaInscripcion::orderByRaw("
            CASE nivel
                WHEN 'licenciatura' THEN 1
                WHEN 'maestria'     THEN 2
                WHEN 'doctorado'    THEN 3
                ELSE 4
            END
        ")->get();

        return view('admin.tarifas.index', compact('tarifas'));
    }

    public function update(Request $request, TarifaInscripcion $tarifa)
    {
        $request->validate([
            'monto' => 'required|numeric|min:1|max:99999.99',
        ]);

        $tarifa->update(['monto' => $request->monto]);

        return redirect()->back()->with('success', 'Tarifa actualizada correctamente.');
    }
}
