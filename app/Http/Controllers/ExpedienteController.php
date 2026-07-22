<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\DocumentoAlumno;
use App\Models\Programa;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    public function index(Request $request)
    {
        $alumnos = Alumno::with(['programa', 'grupo', 'user'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('apellido_paterno', 'like', "%{$q}%")
                        ->orWhere('apellido_materno', 'like', "%{$q}%")
                        ->orWhere('matricula', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('programa'), fn ($query) => $query->where('programa_id', $request->programa))
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->paginate(50)
            ->withQueryString();

        $programas = Programa::orderBy('nombre')->get();

        return view('admin.expedientes.index', compact('alumnos', 'programas'));
    }

    public function show(Alumno $alumno)
    {
        $alumno->load(['programa', 'aspirante', 'documentos']);

        $catalogo   = DocumentoAlumno::catalogoPara($alumno->programa->nivel ?? 'licenciatura');
        $documentos = $alumno->documentos->keyBy('tipo');

        $items = collect($catalogo)->map(function ($item) use ($documentos) {
            $item['documento'] = $documentos->get($item['tipo']);
            return $item;
        });

        return view('admin.expedientes.show', compact('alumno', 'items'));
    }
}
