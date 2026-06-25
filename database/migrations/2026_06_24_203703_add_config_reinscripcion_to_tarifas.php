<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarifas_inscripcion', function (Blueprint $table) {
            $table->unsignedTinyInteger('dias_anticipacion_cobro')->nullable()->after('dias_descuento_pronto_pago');
            $table->unsignedTinyInteger('dias_para_pagar')->nullable()->after('dias_anticipacion_cobro');
        });
    }

    public function down(): void
    {
        Schema::table('tarifas_inscripcion', function (Blueprint $table) {
            $table->dropColumn(['dias_anticipacion_cobro', 'dias_para_pagar']);
        });
    }
};
