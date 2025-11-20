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
        Schema::table('secciones', function (Blueprint $table) {
            $table->integer('anio')->nullable()->after('tipo_mostrar');
            $table->index(['colegio_id', 'tipo_mostrar', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->dropIndex(['colegio_id', 'tipo_mostrar', 'anio']);
            $table->dropColumn('anio');
        });
    }
};
