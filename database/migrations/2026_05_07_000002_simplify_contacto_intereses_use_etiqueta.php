<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar índice único y columna clave de contacto_intereses
        Schema::table('contacto_intereses', function (Blueprint $table) {
            $table->dropUnique('contacto_intereses_clave_unique');
            $table->dropColumn('clave');
        });

        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite implementa el enum como CHECK constraint — hay que recrear la tabla
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement('
                CREATE TABLE contactos_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    nombre VARCHAR(255) NOT NULL,
                    correo VARCHAR(255) NOT NULL,
                    telefono VARCHAR(10) NOT NULL,
                    interes VARCHAR(255) NOT NULL,
                    mensaje TEXT,
                    atendido TINYINT(1) NOT NULL DEFAULT 0,
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ');

            // Copiar datos convirtiendo claves a etiquetas
            foreach (DB::table('contactos')->get() as $row) {
                $map = [
                    'oferta'     => 'Oferta educativa',
                    'admisiones' => 'Proceso de admisión',
                    'estatus'    => 'Estatus de solicitud',
                    'plataforma' => 'Plataforma académica',
                    'otros'      => 'Otros',
                ];
                DB::table('contactos_new')->insert([
                    'id'         => $row->id,
                    'nombre'     => $row->nombre,
                    'correo'     => $row->correo,
                    'telefono'   => $row->telefono,
                    'interes'    => $map[$row->interes] ?? $row->interes,
                    'mensaje'    => $row->mensaje,
                    'atendido'   => $row->atendido,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            DB::statement('DROP TABLE contactos');
            DB::statement('ALTER TABLE contactos_new RENAME TO contactos');
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            // MySQL: actualizar datos y cambiar tipo de columna
            $map = [
                'oferta'     => 'Oferta educativa',
                'admisiones' => 'Proceso de admisión',
                'estatus'    => 'Estatus de solicitud',
                'plataforma' => 'Plataforma académica',
                'otros'      => 'Otros',
            ];
            foreach ($map as $clave => $etiqueta) {
                DB::table('contactos')->where('interes', $clave)->update(['interes' => $etiqueta]);
            }
            DB::statement('ALTER TABLE contactos MODIFY COLUMN interes VARCHAR(255) NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::table('contacto_intereses', function (Blueprint $table) {
            $table->string('clave', 50)->unique()->after('id');
        });
    }
};
