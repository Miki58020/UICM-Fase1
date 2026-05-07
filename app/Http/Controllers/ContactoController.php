<?php

namespace App\Http\Controllers;

use App\Mail\ContactoRecibido;
use App\Models\Contacto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'correo'   => ['required', 'email:rfc,filter', 'max:150'],
            'telefono' => ['required', 'digits:10'],
            'interes'  => ['required', 'in:oferta,admisiones,estatus,plataforma,otros'],
            'mensaje'  => ['nullable', 'string', 'max:1000'],
        ], [
            'nombre.required'   => 'El nombre es obligatorio.',
            'correo.required'   => 'El correo electrónico es obligatorio.',
            'correo.email'      => 'Ingresa un correo electrónico válido.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits'   => 'El teléfono debe tener exactamente 10 dígitos.',
            'interes.required'  => 'Selecciona un interés principal.',
        ]);

        $contacto = Contacto::create($request->only(['nombre', 'correo', 'telefono', 'interes', 'mensaje']));

        $admins = User::where('rol', 'admin')->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ContactoRecibido($contacto));
        }

        return redirect()->route('home', ['#contacto'])->with('contacto_enviado', true);
    }
}
