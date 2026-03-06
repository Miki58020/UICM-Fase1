<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->string('curp_url')->nullable()->after('identificacion_url');
            $table->string('titulo_url')->nullable()->after('curp_url');
        });
    }

    public function down(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->dropColumn(['curp_url', 'titulo_url']);
        });
    }
};
