<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacto_intereses', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 50)->unique();
            $table->string('etiqueta', 100);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('contacto_intereses')->insert([
            ['clave' => 'oferta',     'etiqueta' => 'Oferta educativa',      'orden' => 1, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'admisiones', 'etiqueta' => 'Proceso de admisión',   'orden' => 2, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'estatus',    'etiqueta' => 'Estatus de solicitud',   'orden' => 3, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'plataforma', 'etiqueta' => 'Plataforma académica',   'orden' => 4, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'otros',      'etiqueta' => 'Otros',                  'orden' => 5, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contacto_intereses');
    }
};
