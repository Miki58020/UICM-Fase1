<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 20)->unique();   // ej: IS-101
            $table->string('nombre');
            $table->unsignedTinyInteger('creditos')->default(6);
            $table->unsignedTinyInteger('cuatrimestre'); // en qué cuatrimestre se imparte
            $table->foreignId('programa_id')->constrained('programas');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
