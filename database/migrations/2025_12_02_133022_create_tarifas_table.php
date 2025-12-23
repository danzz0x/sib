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
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_concepto')->constrained('conceptos');
            $table->foreignId('id_tipo_socio')->constrained('tipos_socio');

            // NULL = Tarifa general (SIB) | CON ID = Tarifa específica (Colegio)
            $table->foreignId('id_colegio')->nullable()->constrained('colegios');

            $table->decimal('monto', 10, 2);

            $table->timestamps();

            // Evitar duplicados de precios
            $table->unique(['id_concepto', 'id_tipo_socio', 'id_colegio'], 'tarifa_unica_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifas');
    }
};
