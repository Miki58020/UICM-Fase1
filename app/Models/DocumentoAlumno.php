<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoAlumno extends Model
{
    protected $table = 'documentos_alumno';

    protected $fillable = ['alumno_id', 'tipo', 'archivo_path', 'fecha_subida', 'fecha_vigencia'];

    protected $casts = [
        'fecha_subida'   => 'datetime',
        'fecha_vigencia' => 'date:Y-m-d',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function estaVencido(): bool
    {
        return $this->fecha_vigencia !== null && $this->fecha_vigencia->isPast();
    }

    public function porVencer(): bool
    {
        return $this->fecha_vigencia !== null
            && !$this->estaVencido()
            && $this->fecha_vigencia->lte(now()->addDays(15));
    }

    /**
     * Catálogo de documentos requeridos según el nivel del programa, replicando
     * los mismos documentos que ya se piden en el registro de aspirantes.
     */
    public static function catalogoPara(string $nivel): array
    {
        $catalogo = [
            ['tipo' => 'acta_nacimiento',       'label' => 'Acta de nacimiento',          'dias_vigencia' => null],
            ['tipo' => 'curp',                  'label' => 'CURP',                        'dias_vigencia' => null],
            ['tipo' => 'identificacion',        'label' => 'Identificación oficial',      'dias_vigencia' => null],
            ['tipo' => 'comprobante_domicilio', 'label' => 'Comprobante de domicilio',    'dias_vigencia' => 90],
            ['tipo' => 'foto',                  'label' => 'Fotografía',                  'dias_vigencia' => null],
            [
                'tipo'  => 'certificado',
                'label' => $nivel === 'licenciatura' ? 'Certificado de bachillerato' : 'Título de licenciatura',
                'dias_vigencia' => null,
            ],
        ];

        if ($nivel === 'doctorado') {
            $catalogo[] = ['tipo' => 'titulo', 'label' => 'Título de maestría', 'dias_vigencia' => null];
        }

        return $catalogo;
    }
}
