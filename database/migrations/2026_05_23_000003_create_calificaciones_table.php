<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumno_id');
            $table->unsignedBigInteger('carga_academica_id');
            $table->enum('tipo', ['parcial', 'extraordinario'])->default('parcial');
            $table->unsignedTinyInteger('numero')->default(1); // 1 o 2 para parciales, 1 para extraordinario
            $table->decimal('calificacion', 4, 1);
            $table->timestamps();

            $table->foreign('alumno_id')->references('id')->on('alumnos')->cascadeOnDelete();
            $table->foreign('carga_academica_id')->references('id')->on('carga_academica')->cascadeOnDelete();

            $table->unique(['alumno_id', 'carga_academica_id', 'tipo', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
