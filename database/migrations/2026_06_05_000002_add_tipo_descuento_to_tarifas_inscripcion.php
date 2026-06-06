<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarifas_inscripcion', function (Blueprint $table) {
            $table->string('tipo', 20)->default('inscripcion')->after('nivel');
            $table->decimal('descuento', 5, 2)->default(0.00)->after('monto');
        });

        // SQLite no soporta DROP CONSTRAINT nativo, se maneja con DROP INDEX
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS tarifas_inscripcion_nivel_unique');
            DB::statement('CREATE UNIQUE INDEX tarifas_inscripcion_nivel_tipo_unique ON tarifas_inscripcion (nivel, tipo)');
        } else {
            Schema::table('tarifas_inscripcion', function (Blueprint $table) {
                $table->dropUnique(['nivel']);
                $table->unique(['nivel', 'tipo']);
            });
        }

        // Insertar registros de colegiatura y cuatrimestre basados en los de inscripcion
        foreach (['licenciatura', 'maestria', 'doctorado'] as $nivel) {
            $base = DB::table('tarifas_inscripcion')
                ->where('nivel', $nivel)
                ->where('tipo', 'inscripcion')
                ->first();

            $monto = $base->monto ?? 1500.00;

            foreach (['colegiatura', 'cuatrimestre'] as $tipo) {
                DB::table('tarifas_inscripcion')->insertOrIgnore([
                    'nivel'      => $nivel,
                    'tipo'       => $tipo,
                    'monto'      => $monto,
                    'descuento'  => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('tarifas_inscripcion')
            ->whereIn('tipo', ['colegiatura', 'cuatrimestre'])
            ->delete();

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS tarifas_inscripcion_nivel_tipo_unique');
            DB::statement('CREATE UNIQUE INDEX tarifas_inscripcion_nivel_unique ON tarifas_inscripcion (nivel)');
        } else {
            Schema::table('tarifas_inscripcion', function (Blueprint $table) {
                $table->dropUnique(['nivel', 'tipo']);
                $table->unique(['nivel']);
            });
        }

        Schema::table('tarifas_inscripcion', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'descuento']);
        });
    }
};
