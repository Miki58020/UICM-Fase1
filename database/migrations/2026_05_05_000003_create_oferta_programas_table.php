<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oferta_programas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('nivel', ['licenciatura', 'maestria', 'doctorado']);
            $table->text('descripcion')->nullable();
            $table->json('puntos_clave')->nullable();
            $table->unsignedTinyInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oferta_programas');
    }
};
