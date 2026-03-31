<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 10)->unique();   // ej: 2026-1
            $table->string('label', 80);              // ej: Primer Cuatrimestre 2026
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['proximo', 'activo', 'cerrado'])->default('proximo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos');
    }
};
