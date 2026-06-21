<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_alumno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->string('tipo', 40);
            $table->string('archivo_path');
            $table->timestamp('fecha_subida');
            $table->date('fecha_vigencia')->nullable();
            $table->timestamps();

            $table->unique(['alumno_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_alumno');
    }
};
