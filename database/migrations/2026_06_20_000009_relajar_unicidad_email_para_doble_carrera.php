<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // En SQLite, "alumnos" fue recreada como "alumnos_new" (ver 2026_04_06_000001),
        // dejando el índice con ese nombre. En MySQL la tabla nunca se recreó, así que
        // el índice quedó con el nombre convencional basado en "alumnos".
        $emailUniqueIndex = DB::getDriverName() === 'sqlite'
            ? 'alumnos_new_email_unique'
            : 'alumnos_email_unique';

        Schema::table('alumnos', function (Blueprint $table) use ($emailUniqueIndex) {
            $table->dropUnique($emailUniqueIndex);
            $table->unique(['email', 'programa_id']);
        });

        Schema::table('aspirantes', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->unique(['email', 'programa_id']);
            $table->unique(['curp', 'programa_id']);
        });
    }

    public function down(): void
    {
        $emailUniqueIndex = DB::getDriverName() === 'sqlite'
            ? 'alumnos_new_email_unique'
            : 'alumnos_email_unique';

        Schema::table('alumnos', function (Blueprint $table) use ($emailUniqueIndex) {
            $table->dropUnique(['email', 'programa_id']);
            $table->unique('email', $emailUniqueIndex);
        });

        Schema::table('aspirantes', function (Blueprint $table) {
            $table->dropUnique(['email', 'programa_id']);
            $table->dropUnique(['curp', 'programa_id']);
            $table->unique('email');
        });
    }
};
