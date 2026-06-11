<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarifas_inscripcion', function (Blueprint $table) {
            $table->date('descuento_fecha_inicio')->nullable()->after('descuento');
            $table->date('descuento_fecha_fin')->nullable()->after('descuento_fecha_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('tarifas_inscripcion', function (Blueprint $table) {
            $table->dropColumn(['descuento_fecha_inicio', 'descuento_fecha_fin']);
        });
    }
};
