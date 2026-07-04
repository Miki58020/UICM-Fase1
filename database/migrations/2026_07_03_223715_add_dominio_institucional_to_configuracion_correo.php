<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_correo', function (Blueprint $table) {
            $table->string('dominio_institucional')->default('uicm.edu.mx')->after('from_name');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_correo', function (Blueprint $table) {
            $table->dropColumn('dominio_institucional');
        });
    }
};
