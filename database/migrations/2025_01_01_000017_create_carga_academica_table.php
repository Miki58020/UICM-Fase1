<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carga_academica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('profesor_id')->constrained('profesores');
            $table->string('horario', 50)->nullable();  // ej: Lun-Mié 08:00-10:00
            $table->string('aula', 20)->nullable();     // ej: A-101
            $table->string('ciclo', 10);                // ej: 2026-1
            $table->timestamps();

            // Un grupo no puede tener la misma materia dos veces en el mismo ciclo
            $table->unique(['grupo_id', 'materia_id', 'ciclo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carga_academica');
    }
};
