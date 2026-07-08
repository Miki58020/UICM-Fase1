<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carga_academica', function (Blueprint $table) {
            $table->string('aula', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('carga_academica', function (Blueprint $table) {
            $table->string('aula', 20)->nullable()->change();
        });
    }
};
