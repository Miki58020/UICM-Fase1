<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('calificaciones');

        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumno_id');
            $table->unsignedBigInteger('carga_academica_id');
            $table->enum('tipo', ['final', 'extraordinario'])->default('final');
            $table->decimal('calificacion', 4, 1);
            $table->timestamps();

            $table->foreign('alumno_id')->references('id')->on('alumnos')->cascadeOnDelete();
            $table->foreign('carga_academica_id')->references('id')->on('carga_academica')->cascadeOnDelete();

            $table->unique(['alumno_id', 'carga_academica_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');

        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumno_id');
            $table->unsignedBigInteger('carga_academica_id');
            $table->enum('tipo', ['parcial', 'extraordinario'])->default('parcial');
            $table->unsignedTinyInteger('numero')->default(1);
            $table->decimal('calificacion', 4, 1);
            $table->timestamps();

            $table->foreign('alumno_id')->references('id')->on('alumnos')->cascadeOnDelete();
            $table->foreign('carga_academica_id')->references('id')->on('carga_academica')->cascadeOnDelete();

            $table->unique(['alumno_id', 'carga_academica_id', 'tipo', 'numero']);
        });
    }
};
