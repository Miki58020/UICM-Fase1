<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropUnique('alumnos_new_email_unique');
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
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropUnique(['email', 'programa_id']);
            $table->unique('email', 'alumnos_new_email_unique');
        });

        Schema::table('aspirantes', function (Blueprint $table) {
            $table->dropUnique(['email', 'programa_id']);
            $table->dropUnique(['curp', 'programa_id']);
            $table->unique('email');
        });
    }
};
