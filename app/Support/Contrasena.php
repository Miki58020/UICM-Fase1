<?php

namespace App\Support;

use Illuminate\Validation\Rules\Password;

class Contrasena
{
    /** Longitud de las contraseñas temporales que genera el sistema. */
    private const LONGITUD_TEMPORAL = 12;

    /**
     * Política única de contraseñas: mínimo ocho caracteres, con mayúscula,
     * minúscula y número. Se aplica en todos los puntos donde alguien escribe
     * una contraseña, para que la exigencia no dependa de quién la captura.
     */
    public static function politica(): Password
    {
        return Password::min(8)->mixedCase()->numbers();
    }

    /**
     * Contraseña temporal para las cuentas que el sistema crea por su cuenta
     * (inscripción, alta masiva, restablecimientos). Se arma garantizando que
     * cumpla la misma política en lugar de dejarlo al azar, y se omiten los
     * caracteres que se confunden al leerlos (I, l, 1, O, 0) porque el personal
     * llega a dictarlas o transcribirlas a mano.
     */
    public static function generar(): string
    {
        $conjuntos = [
            'ABCDEFGHJKLMNPQRSTUVWXYZ',
            'abcdefghijkmnopqrstuvwxyz',
            '23456789',
        ];

        $alfabeto = implode('', $conjuntos);

        // Un carácter de cada conjunto asegura que cumpla; el resto es libre.
        $caracteres = array_map(
            fn (string $conjunto) => $conjunto[random_int(0, strlen($conjunto) - 1)],
            $conjuntos
        );

        while (count($caracteres) < self::LONGITUD_TEMPORAL) {
            $caracteres[] = $alfabeto[random_int(0, strlen($alfabeto) - 1)];
        }

        // Sin esto los tres primeros caracteres seguirían siempre el mismo patrón.
        for ($i = count($caracteres) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$caracteres[$i], $caracteres[$j]] = [$caracteres[$j], $caracteres[$i]];
        }

        return implode('', $caracteres);
    }
}
