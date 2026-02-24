<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 10)->unique();
            $table->string('nombre');
            $table->enum('nivel', ['licenciatura', 'maestria', 'doctorado'])->default('licenciatura');
            $table->unsignedTinyInteger('duracion_cuatrimestres')->default(12);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
