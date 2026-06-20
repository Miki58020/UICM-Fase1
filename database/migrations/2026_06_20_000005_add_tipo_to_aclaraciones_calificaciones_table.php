<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aclaraciones_calificaciones', function (Blueprint $table) {
            $table->enum('tipo', ['final', 'extraordinario'])->default('final')->after('carga_academica_id');
        });
    }

    public function down(): void
    {
        Schema::table('aclaraciones_calificaciones', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
