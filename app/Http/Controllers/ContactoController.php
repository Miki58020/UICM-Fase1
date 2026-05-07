<?php

namespace App\Http\Controllers;

use App\Mail\ContactoRecibido;
use App\Mail\ContactoRespuesta;
use App\Models\Contacto;
use App\Models\ContactoInteres;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ContactoController extends Controller
{
    public function index()
    {
        $contactos = Contacto::orderByRaw('atendido ASC, created_at DESC')->get();
        $intereses = ContactoInteres::orderBy('orden')->get();
        return view('admin.contactos.index', compact('contactos', 'intereses'));
    }

    public function responder(Request $request, Contacto $contacto)
    {
        $request->validate([
            'respuesta' => ['required', 'string', 'max:2000'],
        ], [
            'respuesta.required' => 'La respuesta no puede estar vacía.',
        ]);

        Mail::to($contacto->correo)->send(new ContactoRespuesta($contacto, $request->respuesta));

        $contacto->update(['atendido' => true]);

        return back()->with('success', 'Respuesta enviada a ' . $contacto->correo . '.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'correo'   => ['required', 'email:rfc,filter', 'max:150'],
            'telefono' => ['required', 'digits:10'],
            'interes'  => ['required', Rule::in(ContactoInteres::where('activo', true)->pluck('etiqueta'))],
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
