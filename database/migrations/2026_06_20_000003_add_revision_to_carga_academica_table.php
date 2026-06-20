<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carga_academica', function (Blueprint $table) {
            $table->enum('estado_revision', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente')->after('fecha_fin');
            $table->text('motivo_rechazo')->nullable()->after('estado_revision');
            $table->foreignId('revisado_por')->nullable()->after('motivo_rechazo')->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_at')->nullable()->after('revisado_por');
        });
    }

    public function down(): void
    {
        Schema::table('carga_academica', function (Blueprint $table) {
            $table->dropConstrainedForeignId('revisado_por');
            $table->dropColumn(['estado_revision', 'motivo_rechazo', 'revisado_at']);
        });
    }
};
