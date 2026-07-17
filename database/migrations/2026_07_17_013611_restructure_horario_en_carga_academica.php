<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('carga_academica', function (Blueprint $table) {
            $table->dropColumn('horario');
            $table->string('dia_semana', 20)->nullable()->after('profesor_id');
            $table->string('hora_inicio', 5)->nullable()->after('dia_semana');
            $table->string('hora_fin', 5)->nullable()->after('hora_inicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carga_academica', function (Blueprint $table) {
            $table->dropColumn(['dia_semana', 'hora_inicio', 'hora_fin']);
            $table->string('horario', 50)->nullable()->after('profesor_id');
        });
    }
};
