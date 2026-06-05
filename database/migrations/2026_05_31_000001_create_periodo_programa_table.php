<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodo_programa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periodo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('programa_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('numero_carrera');
            $table->unsignedSmallInteger('numero_generacion');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['periodo_id', 'programa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodo_programa');
    }
};
