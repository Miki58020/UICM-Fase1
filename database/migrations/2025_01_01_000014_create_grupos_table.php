<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 20)->unique();
            $table->foreignId('programa_id')->constrained('programas');
            $table->foreignId('periodo_id')->constrained('periodos');
            $table->unsignedTinyInteger('cuatrimestre');
            $table->unsignedSmallInteger('capacidad')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
