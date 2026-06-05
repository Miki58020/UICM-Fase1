<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('PRAGMA foreign_keys = OFF');

        DB::unprepared('
            CREATE TABLE periodos_new (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre     VARCHAR(10)  NOT NULL UNIQUE,
                label      VARCHAR(80)  NOT NULL,
                fecha_inicio_registro  DATE NULL,
                fecha_fin_registro     DATE NULL,
                fecha_inicio_clases    DATE NULL,
                fecha_fin_clases       DATE NULL,
                estado     VARCHAR(255) NOT NULL DEFAULT \'proximo\',
                auto       TINYINT(1)   NOT NULL DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )
        ');

        DB::unprepared('
            INSERT INTO periodos_new
                (id, nombre, label, fecha_inicio_registro, fecha_fin_registro,
                 fecha_inicio_clases, fecha_fin_clases, estado, auto, created_at, updated_at)
            SELECT
                id, nombre, label, fecha_inicio_registro, fecha_fin_registro,
                fecha_inicio_clases, fecha_fin_clases, estado, auto, created_at, updated_at
            FROM periodos
        ');

        DB::unprepared('DROP TABLE periodos');
        DB::unprepared('ALTER TABLE periodos_new RENAME TO periodos');

        DB::unprepared('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        DB::unprepared('PRAGMA foreign_keys = OFF');

        DB::unprepared('
            CREATE TABLE periodos_new (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre     VARCHAR(10)  NOT NULL UNIQUE,
                label      VARCHAR(80)  NOT NULL,
                fecha_inicio_registro  DATE NOT NULL,
                fecha_fin_registro     DATE NOT NULL,
                fecha_inicio_clases    DATE NULL,
                fecha_fin_clases       DATE NULL,
                estado     VARCHAR(255) NOT NULL DEFAULT \'proximo\',
                auto       TINYINT(1)   NOT NULL DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )
        ');

        DB::unprepared('
            INSERT INTO periodos_new
                (id, nombre, label, fecha_inicio_registro, fecha_fin_registro,
                 fecha_inicio_clases, fecha_fin_clases, estado, auto, created_at, updated_at)
            SELECT
                id, nombre, label, fecha_inicio_registro, fecha_fin_registro,
                fecha_inicio_clases, fecha_fin_clases, estado, auto, created_at, updated_at
            FROM periodos
        ');

        DB::unprepared('DROP TABLE periodos');
        DB::unprepared('ALTER TABLE periodos_new RENAME TO periodos');

        DB::unprepared('PRAGMA foreign_keys = ON');
    }
};
