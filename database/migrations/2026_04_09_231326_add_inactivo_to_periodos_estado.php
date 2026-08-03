<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE periodos MODIFY estado ENUM('proximo', 'activo', 'cerrado', 'inactivo') NOT NULL DEFAULT 'inactivo'");

            return;
        }

        // SQLite no permite modificar CHECK constraints directamente.
        // Se recrea la tabla con el nuevo estado permitido.
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE periodos_new (
                id                     INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre                 VARCHAR(10) NOT NULL UNIQUE,
                label                  VARCHAR(80) NOT NULL,
                fecha_inicio_registro  DATE NOT NULL,
                fecha_fin_registro     DATE NOT NULL,
                estado                 VARCHAR CHECK (estado IN (\'proximo\', \'activo\', \'cerrado\', \'inactivo\')) NOT NULL DEFAULT \'inactivo\',
                created_at             DATETIME,
                updated_at             DATETIME,
                fecha_inicio_clases    DATE,
                fecha_fin_clases       DATE,
                auto                   TINYINT(1) NOT NULL DEFAULT 1
            )
        ');

        DB::statement('INSERT INTO periodos_new SELECT * FROM periodos');
        DB::statement('DROP TABLE periodos');
        DB::statement('ALTER TABLE periodos_new RENAME TO periodos');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE periodos MODIFY estado ENUM('proximo', 'activo', 'cerrado') NOT NULL DEFAULT 'proximo'");

            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE periodos_new (
                id                     INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre                 VARCHAR(10) NOT NULL UNIQUE,
                label                  VARCHAR(80) NOT NULL,
                fecha_inicio_registro  DATE NOT NULL,
                fecha_fin_registro     DATE NOT NULL,
                estado                 VARCHAR CHECK (estado IN (\'proximo\', \'activo\', \'cerrado\')) NOT NULL DEFAULT \'proximo\',
                created_at             DATETIME,
                updated_at             DATETIME,
                fecha_inicio_clases    DATE,
                fecha_fin_clases       DATE,
                auto                   TINYINT(1) NOT NULL DEFAULT 1
            )
        ');

        DB::statement('INSERT INTO periodos_new SELECT * FROM periodos');
        DB::statement('DROP TABLE periodos');
        DB::statement('ALTER TABLE periodos_new RENAME TO periodos');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
