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
        Schema::create('socios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_tipo_socio')->constrained('tipos_socio');
            $table->foreignId('id_colegio')->constrained('colegios');

            $table->string('nombre', 150);
            $table->string('cedula', 20)->unique();
            $table->string('rni', 20)->unique()->nullable();
            $table->string('especialidad');
            $table->string('email', 100)->unique()->nullable();
            $table->string('telefono', 20)->nullable();
            $table->date('fecha_registro');
            $table->string('estado', 20)->default('Activo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socios');
    }
};
