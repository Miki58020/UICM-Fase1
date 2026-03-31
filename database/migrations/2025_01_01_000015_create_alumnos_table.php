<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('matricula', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('aspirante_id')->nullable()->constrained('aspirantes')->nullOnDelete();
            $table->foreignId('programa_id')->constrained('programas');
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->nullOnDelete();
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->string('email')->unique();
            $table->unsignedTinyInteger('cuatrimestre_actual')->default(1);
            $table->unsignedSmallInteger('creditos_acumulados')->default(0);
            $table->enum('estado', ['activo', 'baja_temporal', 'baja_definitiva', 'egresado'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
