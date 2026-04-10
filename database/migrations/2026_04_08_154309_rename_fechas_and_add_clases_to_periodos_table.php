<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periodos', function (Blueprint $table) {
            $table->renameColumn('fecha_inicio', 'fecha_inicio_registro');
            $table->renameColumn('fecha_fin', 'fecha_fin_registro');
            $table->date('fecha_inicio_clases')->nullable()->after('fecha_fin_registro');
            $table->date('fecha_fin_clases')->nullable()->after('fecha_inicio_clases');
        });
    }

    public function down(): void
    {
        Schema::table('periodos', function (Blueprint $table) {
            $table->renameColumn('fecha_inicio_registro', 'fecha_inicio');
            $table->renameColumn('fecha_fin_registro', 'fecha_fin');
            $table->dropColumn(['fecha_inicio_clases', 'fecha_fin_clases']);
        });
    }
};
