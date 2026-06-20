<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aclaraciones_calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('carga_academica_id')->constrained('carga_academica')->cascadeOnDelete();
            $table->foreignId('profesor_id')->constrained('profesores')->cascadeOnDelete();
            $table->decimal('calificacion_propuesta', 4, 1);
            $table->text('motivo');
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_at')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aclaraciones_calificaciones');
    }
};
