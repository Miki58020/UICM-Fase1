<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->date('fecha_nacimiento')->nullable()->after('curp');
            $table->string('generacion', 10)->nullable()->after('programa_id');
            $table->string('acta_nacimiento_url')->nullable()->after('generacion');
            $table->string('certificado_url')->nullable()->after('acta_nacimiento_url');
            $table->string('identificacion_url')->nullable()->after('certificado_url');
        });
    }

    public function down(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->dropColumn(['fecha_nacimiento', 'generacion', 'acta_nacimiento_url', 'certificado_url', 'identificacion_url']);
        });
    }
};
