<?php

namespace App\Http\Controllers;

use App\Mail\ContrasenaActualizada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto.image'    => 'El archivo debe ser una imagen.',
            'foto.mimes'    => 'Solo se permiten formatos JPG, PNG o WEBP.',
            'foto.max'      => 'La imagen no debe superar 2 MB.',
        ]);

        $user = auth()->user();

        if ($user->foto) {
            Storage::disk('local')->delete($user->foto);
        }

        $path = $request->file('foto')->store('perfiles', 'local');
        $user->update(['foto' => $path]);

        return redirect()->route('dashboard')->with('success', 'Foto de perfil actualizada.');
    }

    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required'  => 'Ingresa la nueva contraseña.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = auth()->user();
        $user->update(['password' => Hash::make($request->password)]);

        Mail::to($user->email)->send(new ContrasenaActualizada($user, $request->password));

        return redirect()->route('dashboard')->with('success', 'Contraseña actualizada correctamente. Se envió confirmación a tu correo.');
    }
}
