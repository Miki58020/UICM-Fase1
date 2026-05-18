<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracion_mercadopago', function (Blueprint $table) {
            $table->id();
            $table->string('public_key');
            $table->string('access_token');
            $table->string('back_url_success');
            $table->string('back_url_pending');
            $table->string('back_url_failure');
            $table->string('notification_url')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_mercadopago');
    }
};
