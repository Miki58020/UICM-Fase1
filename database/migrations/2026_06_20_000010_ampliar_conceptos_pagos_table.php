<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspirante_id')->nullable()->constrained('aspirantes')->nullOnDelete();
            $table->foreignId('alumno_id')->nullable()->constrained('alumnos')->nullOnDelete();
            $table->enum('concepto', ['inscripcion', 'reinscripcion', 'cuatrimestre', 'colegiatura', 'otro'])->default('inscripcion');
            $table->string('periodo', 10);
            $table->string('mes', 7)->nullable();
            $table->decimal('monto', 10, 2);
            $table->decimal('descuento', 5, 2)->default(0);
            $table->decimal('monto_original', 10, 2)->nullable();
            $table->string('comprobante')->nullable();
            $table->string('mp_preference_id')->nullable();
            $table->string('mp_payment_id')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        DB::statement('INSERT INTO pagos_new (id, aspirante_id, alumno_id, concepto, periodo, monto, descuento, monto_original, comprobante, mp_preference_id, mp_payment_id, fecha_pago, estado, observaciones, created_at, updated_at)
            SELECT id, aspirante_id, alumno_id, concepto, periodo, monto, descuento, monto_original, comprobante, mp_preference_id, mp_payment_id, fecha_pago, estado, observaciones, created_at, updated_at FROM pagos');

        Schema::drop('pagos');
        Schema::rename('pagos_new', 'pagos');
    }

    public function down(): void
    {
        Schema::create('pagos_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspirante_id')->nullable()->constrained('aspirantes')->nullOnDelete();
            $table->foreignId('alumno_id')->nullable()->constrained('alumnos')->nullOnDelete();
            $table->enum('concepto', ['inscripcion', 'reinscripcion', 'otro'])->default('inscripcion');
            $table->string('periodo', 10);
            $table->decimal('monto', 10, 2);
            $table->decimal('descuento', 5, 2)->default(0);
            $table->decimal('monto_original', 10, 2)->nullable();
            $table->string('comprobante')->nullable();
            $table->string('mp_preference_id')->nullable();
            $table->string('mp_payment_id')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        DB::statement('INSERT INTO pagos_old (id, aspirante_id, alumno_id, concepto, periodo, monto, descuento, monto_original, comprobante, mp_preference_id, mp_payment_id, fecha_pago, estado, observaciones, created_at, updated_at)
            SELECT id, aspirante_id, alumno_id, concepto, periodo, monto, descuento, monto_original, comprobante, mp_preference_id, mp_payment_id, fecha_pago, estado, observaciones, created_at, updated_at FROM pagos');

        Schema::drop('pagos');
        Schema::rename('pagos_old', 'pagos');
    }
};
