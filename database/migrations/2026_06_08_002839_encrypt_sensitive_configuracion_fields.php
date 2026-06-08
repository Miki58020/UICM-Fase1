<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ensancha las columnas a TEXT (el valor cifrado ocupa más que el original)
     * y cifra los valores existentes que aún estén en texto plano.
     */
    public function up(): void
    {
        Schema::table('configuracion_mercadopago', function (Blueprint $table) {
            $table->text('access_token')->change();
        });

        Schema::table('configuracion_correo', function (Blueprint $table) {
            $table->text('password')->nullable()->change();
        });

        $this->cifrar('configuracion_mercadopago', 'access_token');
        $this->cifrar('configuracion_correo', 'password');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->descifrar('configuracion_mercadopago', 'access_token');
        $this->descifrar('configuracion_correo', 'password');

        Schema::table('configuracion_mercadopago', function (Blueprint $table) {
            $table->string('access_token')->change();
        });

        Schema::table('configuracion_correo', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Cifra los valores que aún estén en texto plano (idempotente: si ya
     * descifra correctamente, asumimos que ya está cifrado y se deja igual).
     */
    private function cifrar(string $tabla, string $columna): void
    {
        foreach (DB::table($tabla)->select('id', $columna)->get() as $fila) {
            $valor = $fila->$columna;

            if ($valor === null || $valor === '') {
                continue;
            }

            try {
                Crypt::decryptString($valor);
                continue; // ya estaba cifrado
            } catch (\Throwable $e) {
                // texto plano: cifrar
            }

            DB::table($tabla)->where('id', $fila->id)->update([
                $columna => Crypt::encryptString($valor),
            ]);
        }
    }

    /**
     * Devuelve los valores cifrados a texto plano (rollback).
     */
    private function descifrar(string $tabla, string $columna): void
    {
        foreach (DB::table($tabla)->select('id', $columna)->get() as $fila) {
            $valor = $fila->$columna;

            if ($valor === null || $valor === '') {
                continue;
            }

            try {
                $plano = Crypt::decryptString($valor);
            } catch (\Throwable $e) {
                continue; // ya estaba en texto plano
            }

            DB::table($tabla)->where('id', $fila->id)->update([
                $columna => $plano,
            ]);
        }
    }
};
