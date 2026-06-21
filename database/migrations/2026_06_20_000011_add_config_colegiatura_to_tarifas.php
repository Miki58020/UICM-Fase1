<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarifas_inscripcion', function (Blueprint $table) {
            $table->unsignedTinyInteger('dia_limite_pago')->nullable()->after('descuento_fecha_fin');
            $table->unsignedTinyInteger('dias_descuento_pronto_pago')->nullable()->after('dia_limite_pago');
        });
    }

    public function down(): void
    {
        Schema::table('tarifas_inscripcion', function (Blueprint $table) {
            $table->dropColumn(['dia_limite_pago', 'dias_descuento_pronto_pago']);
        });
    }
};
