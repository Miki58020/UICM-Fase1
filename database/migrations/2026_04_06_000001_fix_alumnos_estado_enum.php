<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('alumnos_new', function (Blueprint $table) {
            $table->id();
            $table->string('matricula', 20)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('aspirante_id')->nullable();
            $table->unsignedBigInteger('programa_id');
            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->string('email')->unique();
            $table->unsignedTinyInteger('cuatrimestre_actual')->default(1);
            $table->unsignedSmallInteger('creditos_acumulados')->default(0);
            $table->enum('estado', ['activo', 'inactivo', 'baja'])->default('activo');
            $table->timestamps();
        });

        DB::statement("
            INSERT INTO alumnos_new
                (id, matricula, user_id, aspirante_id, programa_id, grupo_id,
                 nombre, apellido_paterno, apellido_materno, email,
                 cuatrimestre_actual, creditos_acumulados, estado, created_at, updated_at)
            SELECT
                id, matricula, user_id, aspirante_id, programa_id, grupo_id,
                nombre, apellido_paterno, apellido_materno, email,
                cuatrimestre_actual, creditos_acumulados,
                CASE estado
                    WHEN 'baja_temporal'   THEN 'inactivo'
                    WHEN 'baja_definitiva' THEN 'baja'
                    WHEN 'egresado'        THEN 'baja'
                    ELSE estado
                END,
                created_at, updated_at
            FROM alumnos
        ");

        Schema::drop('alumnos');
        DB::statement('ALTER TABLE alumnos_new RENAME TO alumnos');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('alumnos_old', function (Blueprint $table) {
            $table->id();
            $table->string('matricula', 20)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('aspirante_id')->nullable();
            $table->unsignedBigInteger('programa_id');
            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->string('email')->unique();
            $table->unsignedTinyInteger('cuatrimestre_actual')->default(1);
            $table->unsignedSmallInteger('creditos_acumulados')->default(0);
            $table->enum('estado', ['activo', 'baja_temporal', 'baja_definitiva', 'egresado'])->default('activo');
            $table->timestamps();
        });

        DB::statement("
            INSERT INTO alumnos_old
                (id, matricula, user_id, aspirante_id, programa_id, grupo_id,
                 nombre, apellido_paterno, apellido_materno, email,
                 cuatrimestre_actual, creditos_acumulados, estado, created_at, updated_at)
            SELECT
                id, matricula, user_id, aspirante_id, programa_id, grupo_id,
                nombre, apellido_paterno, apellido_materno, email,
                cuatrimestre_actual, creditos_acumulados,
                CASE estado
                    WHEN 'inactivo' THEN 'baja_temporal'
                    WHEN 'baja'     THEN 'baja_definitiva'
                    ELSE estado
                END,
                created_at, updated_at
            FROM alumnos
        ");

        Schema::drop('alumnos');
        DB::statement('ALTER TABLE alumnos_old RENAME TO alumnos');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
