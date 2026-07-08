<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\DocumentoAlumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentoAlumnoController extends Controller
{
    public function index()
    {
        $alumno = Alumno::where('user_id', Auth::id())->with('programa')->firstOrFail();

        $catalogo = DocumentoAlumno::catalogoPara($alumno->programa->nivel ?? 'licenciatura');
        $documentos = $alumno->documentos->keyBy('tipo');

        $items = collect($catalogo)->map(function ($item) use ($documentos) {
            $item['documento'] = $documentos->get($item['tipo']);
            return $item;
        });

        $faltantes = $items->filter(fn($item) => !$item['documento'])->count();
        $vencidos  = $items->filter(fn($item) => $item['documento']?->estaVencido())->count();
        $porVencer = $items->filter(fn($item) => $item['documento']?->porVencer())->count();
        $vigentes  = $items->count() - $faltantes - $vencidos - $porVencer;

        // Alumnos migrados no llegan con documentos precargados: se les da 1 mes
        // desde su alta para completar su expediente, sin ninguna sanción.
        $plazoMigrado = ($alumno->migrado && $faltantes > 0)
            ? $alumno->created_at->copy()->addMonth()
            : null;

        return view('alumno.documentos.index', compact(
            'alumno', 'items', 'vencidos', 'porVencer', 'vigentes', 'faltantes', 'plazoMigrado'
        ));
    }

    public function subir(Request $request, string $tipo)
    {
        $alumno = Alumno::where('user_id', Auth::id())->with('programa')->firstOrFail();

        $tiposValidos = collect(DocumentoAlumno::catalogoPara($alumno->programa->nivel ?? 'licenciatura'))
            ->pluck('tipo');

        abort_if(!$tiposValidos->contains($tipo), 404);

        $request->validate([
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $documentoAnterior = DocumentoAlumno::where('alumno_id', $alumno->id)->where('tipo', $tipo)->first();
        if ($documentoAnterior) {
            Storage::disk('local')->delete($documentoAnterior->archivo_path);
        }

        $path = $request->file('archivo')->store("documentos/alumnos/{$tipo}", 'local');

        $diasVigencia = collect(DocumentoAlumno::catalogoPara($alumno->programa->nivel ?? 'licenciatura'))
            ->firstWhere('tipo', $tipo)['dias_vigencia'] ?? null;

        DocumentoAlumno::updateOrCreate(
            ['alumno_id' => $alumno->id, 'tipo' => $tipo],
            [
                'archivo_path'   => $path,
                'fecha_subida'   => now(),
                'fecha_vigencia' => $diasVigencia ? now()->addDays($diasVigencia)->toDateString() : null,
            ]
        );

        return back()->with('success', 'Documento actualizado correctamente.');
    }
}
