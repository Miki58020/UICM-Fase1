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
            $table->foreignId('profesor_id')->nullable()->constrained('profesores')->nullOnDelete();
            $table->string('horario', 50)->nullable();
            $table->string('aula', 20)->nullable();
            $table->foreignId('periodo_id')->constrained('periodos');
            $table->timestamps();

            $table->unique(['grupo_id', 'materia_id', 'periodo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carga_academica');
    }
};
