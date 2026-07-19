<?php

namespace App\Support;

use Normalizer;

class Texto
{
    /**
     * Nombres y apellidos se guardan siempre en mayúsculas y sin acentos
     * (mismo criterio en toda la captura de personas: aspirantes, alumnos migrados, etc.).
     */
    public static function normalizarNombre(?string $texto): ?string
    {
        if ($texto === null) {
            return null;
        }

        $texto = normalizer_normalize($texto, Normalizer::FORM_D);
        $texto = preg_replace('/\p{Mn}/u', '', $texto);

        return strtoupper($texto);
    }
}
