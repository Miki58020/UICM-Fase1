<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('curp', 18)->nullable()->after('migrado');
            $table->string('telefono', 20)->nullable()->after('curp');
            $table->date('fecha_nacimiento')->nullable()->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn(['curp', 'telefono', 'fecha_nacimiento']);
        });
    }
};
