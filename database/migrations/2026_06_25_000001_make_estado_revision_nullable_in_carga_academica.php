<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carga_academica', function (Blueprint $table) {
            $table->string('estado_revision', 20)->nullable()->default(null)->change();
        });

        // Cargas en 'pendiente' sin ninguna calificación todavía → null (el profe no ha enviado nada)
        DB::statement("
            UPDATE carga_academica
            SET estado_revision = NULL
            WHERE estado_revision = 'pendiente'
            AND id NOT IN (SELECT DISTINCT carga_academica_id FROM calificaciones)
        ");
    }

    public function down(): void
    {
        DB::statement("UPDATE carga_academica SET estado_revision = 'pendiente' WHERE estado_revision IS NULL");

        Schema::table('carga_academica', function (Blueprint $table) {
            $table->enum('estado_revision', ['pendiente', 'aprobado', 'rechazado'])
                  ->default('pendiente')
                  ->change();
        });
    }
};
