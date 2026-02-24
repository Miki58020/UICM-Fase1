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
            $table->string('clave', 20)->unique();  // ej: IS-101
            $table->foreignId('programa_id')->constrained('programas');
            $table->string('ciclo', 10);             // ej: 2026-1
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
