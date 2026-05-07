<?php

namespace App\Http\Controllers;

use App\Models\CarruselImagen;
use App\Models\ConfiguracionSitio;
use App\Models\ContactoInteres;
use App\Models\OfertaPrograma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaginaPrincipalController extends Controller
{
    public function index()
    {
        $imagenes  = CarruselImagen::orderBy('orden')->get();
        $programas = OfertaPrograma::orderBy('orden')->get();
        $intereses = ContactoInteres::orderBy('orden')->get();
        $contacto  = [
            'correo'   => ConfiguracionSitio::get('correo',   'contacto@uicm.edu.mx'),
            'telefono' => ConfiguracionSitio::get('telefono', '(55) 0000 0000'),
            'horario'  => ConfiguracionSitio::get('horario',  'Lunes a viernes de 9:00 a 18:00 hrs.'),
        ];

        return view('admin.pagina-principal.index', compact('imagenes', 'programas', 'contacto', 'intereses'));
    }

    // ── Contacto ─────────────────────────────────────────────────────────────

    public function updateContacto(Request $request)
    {
        $request->validate([
            'correo'   => 'required|email|max:100',
            'telefono' => 'required|string|max:30',
            'horario'  => 'required|string|max:100',
        ]);

        ConfiguracionSitio::set('correo',   $request->correo);
        ConfiguracionSitio::set('telefono', $request->telefono);
        ConfiguracionSitio::set('horario',  $request->horario);

        return back()->with('success_contacto', 'Información de contacto actualizada.')->with('tab_activo', 'contacto');
    }

    // ── Carrusel ──────────────────────────────────────────────────────────────

    const MAX_ACTIVAS = 8;
    const MAX_TOTAL   = 20;

    public function storeImagen(Request $request)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'imagen.required' => 'Selecciona una imagen.',
            'imagen.image'    => 'El archivo debe ser una imagen.',
            'imagen.mimes'    => 'Formatos permitidos: jpg, jpeg, png, webp.',
            'imagen.max'      => 'La imagen no puede superar 4 MB.',
        ]);

        if (CarruselImagen::count() >= self::MAX_TOTAL) {
            return back()->with('success_carrusel', null)
                ->withErrors(['imagen' => 'Límite de almacenamiento alcanzado (' . self::MAX_TOTAL . ' imágenes). Elimina alguna para continuar.'])
                ->with('tab_activo', 'carrusel');
        }

        $path    = $request->file('imagen')->store('carrusel', 'public');
        $orden   = CarruselImagen::max('orden') + 1;
        $activas = CarruselImagen::where('activo', true)->count();

        CarruselImagen::create([
            'imagen_path' => $path,
            'orden'       => $orden,
            'activo'      => $activas < self::MAX_ACTIVAS,
        ]);

        $msg = $activas < self::MAX_ACTIVAS
            ? 'Imagen agregada al carrusel.'
            : 'Imagen guardada como inactiva (ya hay ' . self::MAX_ACTIVAS . ' activas). Oculta alguna para activarla.';

        return back()->with('success_carrusel', $msg)->with('tab_activo', 'carrusel');
    }

    public function destroyImagen(CarruselImagen $imagen)
    {
        Storage::disk('public')->delete($imagen->imagen_path);
        $imagen->delete();

        return back()->with('success_carrusel', 'Imagen eliminada del carrusel.')->with('tab_activo', 'carrusel');
    }

    public function toggleImagen(CarruselImagen $imagen)
    {
        if (!$imagen->activo && CarruselImagen::where('activo', true)->count() >= self::MAX_ACTIVAS) {
            return back()->with('tab_activo', 'carrusel')
                ->withErrors(['imagen' => 'Ya hay ' . self::MAX_ACTIVAS . ' imágenes activas. Oculta alguna antes de activar otra.']);
        }

        $imagen->update(['activo' => !$imagen->activo]);
        return back()->with('success_carrusel', 'Estado de la imagen actualizado.')->with('tab_activo', 'carrusel');
    }

    // ── Oferta educativa ──────────────────────────────────────────────────────

    public function storePrograma(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:150',
            'nivel'       => 'required|in:licenciatura,maestria,doctorado',
            'descripcion' => 'nullable|string|max:600',
            'puntos_clave' => 'nullable|string',
        ]);

        $puntos = $this->parsearPuntos($request->puntos_clave);
        $orden  = OfertaPrograma::max('orden') + 1;

        OfertaPrograma::create([
            'nombre'      => $request->nombre,
            'nivel'       => $request->nivel,
            'descripcion' => $request->descripcion,
            'puntos_clave' => $puntos,
            'orden'       => $orden,
        ]);

        return back()->with('success_oferta', 'Programa agregado a la oferta educativa.')->with('tab_activo', 'oferta');
    }

    public function updatePrograma(Request $request, OfertaPrograma $programa)
    {
        $request->validate([
            'nombre'      => 'required|string|max:150',
            'nivel'       => 'required|in:licenciatura,maestria,doctorado',
            'descripcion' => 'nullable|string|max:600',
            'puntos_clave' => 'nullable|string',
        ]);

        $programa->update([
            'nombre'      => $request->nombre,
            'nivel'       => $request->nivel,
            'descripcion' => $request->descripcion,
            'puntos_clave' => $this->parsearPuntos($request->puntos_clave),
        ]);

        return back()->with('success_oferta', 'Programa actualizado correctamente.')->with('tab_activo', 'oferta');
    }

    public function destroyPrograma(OfertaPrograma $programa)
    {
        $programa->delete();
        return back()->with('success_oferta', 'Programa eliminado de la oferta educativa.')->with('tab_activo', 'oferta');
    }

    public function togglePrograma(OfertaPrograma $programa)
    {
        $programa->update(['activo' => !$programa->activo]);
        return back()->with('success_oferta', 'Estado del programa actualizado.')->with('tab_activo', 'oferta');
    }

    // ── Intereses de contacto ─────────────────────────────────────────────────

    public function storeInteres(Request $request)
    {
        $request->validate([
            'etiqueta' => 'required|string|max:100|unique:contacto_intereses,etiqueta',
        ], [
            'etiqueta.required' => 'La etiqueta es obligatoria.',
            'etiqueta.unique'   => 'Ya existe un interés con esa etiqueta.',
        ]);

        $orden = ContactoInteres::max('orden') + 1;
        ContactoInteres::create([
            'etiqueta' => $request->etiqueta,
            'orden'    => $orden,
        ]);

        return back()->with('success_contacto', 'Interés agregado correctamente.')->with('tab_activo', 'contacto');
    }

    public function updateInteres(Request $request, ContactoInteres $interes)
    {
        $request->validate([
            'etiqueta' => 'required|string|max:100',
        ], [
            'etiqueta.required' => 'La etiqueta es obligatoria.',
        ]);

        $interes->update(['etiqueta' => $request->etiqueta]);

        return back()->with('success_contacto', 'Interés actualizado.')->with('tab_activo', 'contacto');
    }

    public function destroyInteres(ContactoInteres $interes)
    {
        $interes->delete();
        return back()->with('success_contacto', 'Interés eliminado.')->with('tab_activo', 'contacto');
    }

    public function toggleInteres(ContactoInteres $interes)
    {
        $interes->update(['activo' => !$interes->activo]);
        return back()->with('success_contacto', 'Estado del interés actualizado.')->with('tab_activo', 'contacto');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function parsearPuntos(?string $texto): array
    {
        if (!$texto) return [];

        return collect(explode("\n", $texto))
            ->map(fn($l) => trim($l))
            ->filter()
            ->values()
            ->all();
    }
}
