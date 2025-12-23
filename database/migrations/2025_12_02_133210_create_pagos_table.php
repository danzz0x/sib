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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_socio')->constrained('socios');
            $table->foreignId('id_concepto')->constrained('conceptos');

            // IMPORTANTE: Nullable para permitir que el socio registre el pago desde la web
            // Se llenará con el ID del secretario cuando este apruebe el pago.
            $table->foreignId('id_usuario')->nullable()->constrained('users');

            $table->foreignId('id_metodo_pago')->constrained('metodos_pago');

            $table->decimal('monto_pagado', 10, 2);

            // Datos de verificación del pago
            $table->string('nro_transaccion', 100)->nullable(); // Null si es efectivo
            $table->string('comprobante_path', 255)->nullable(); // Foto del recibo

            $table->dateTime('fecha_pago');

            // Periodos cubiertos
            $table->date('periodo_inicio')->nullable();
            $table->date('periodo_fin')->nullable();

            // Estados del flujo: 'pendiente' (recién subido), 'aprobado' (dinero en banco), 'rechazado'
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');

            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
