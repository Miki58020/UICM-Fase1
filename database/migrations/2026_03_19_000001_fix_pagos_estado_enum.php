<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite no soporta ALTER COLUMN, hay que recrear la tabla
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE pagos_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                aspirante_id INTEGER REFERENCES aspirantes(id) ON DELETE SET NULL,
                alumno_id INTEGER REFERENCES alumnos(id) ON DELETE SET NULL,
                concepto TEXT NOT NULL DEFAULT "inscripcion" CHECK(concepto IN ("inscripcion","reinscripcion","otro")),
                periodo TEXT NOT NULL,
                monto NUMERIC NOT NULL,
                comprobante TEXT,
                mp_preference_id TEXT,
                mp_payment_id TEXT,
                fecha_pago DATE,
                estado TEXT NOT NULL DEFAULT "pendiente" CHECK(estado IN ("pendiente","aprobado","rechazado")),
                observaciones TEXT,
                created_at DATETIME,
                updated_at DATETIME
            )
        ');

        DB::statement('INSERT INTO pagos_new SELECT
            id, aspirante_id, alumno_id, concepto, periodo, monto,
            comprobante, mp_preference_id, mp_payment_id, fecha_pago,
            CASE estado WHEN "validado" THEN "aprobado" ELSE estado END,
            observaciones, created_at, updated_at
            FROM pagos
        ');

        Schema::drop('pagos');

        DB::statement('ALTER TABLE pagos_new RENAME TO pagos');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE pagos_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                aspirante_id INTEGER REFERENCES aspirantes(id) ON DELETE SET NULL,
                alumno_id INTEGER REFERENCES alumnos(id) ON DELETE SET NULL,
                concepto TEXT NOT NULL DEFAULT "inscripcion" CHECK(concepto IN ("inscripcion","reinscripcion","otro")),
                periodo TEXT NOT NULL,
                monto NUMERIC NOT NULL,
                comprobante TEXT,
                mp_preference_id TEXT,
                mp_payment_id TEXT,
                fecha_pago DATE,
                estado TEXT NOT NULL DEFAULT "pendiente" CHECK(estado IN ("pendiente","validado","rechazado")),
                observaciones TEXT,
                created_at DATETIME,
                updated_at DATETIME
            )
        ');

        DB::statement('INSERT INTO pagos_new SELECT * FROM pagos');

        Schema::drop('pagos');

        DB::statement('ALTER TABLE pagos_new RENAME TO pagos');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
