<?php

namespace Database\Seeders;

use App\Models\Colegio;
use App\Models\Tarifa;
use Illuminate\Database\Seeder;

class TarifasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // 1. APORTE SIB (General para todos)
        // ==========================================

        // Ingeniero paga 50 a la SIB Nacional/Departamental
        Tarifa::create([
            'id_concepto' => 1, // Aporte SIB
            'id_tipo_socio' => 1, // Ingeniero
            'id_colegio' => null, // Aplica a todos
            'monto' => 50.00,
        ]);

        // Técnico paga 17.40 a la SIB Nacional/Departamental
        Tarifa::create([
            'id_concepto' => 1, // Aporte SIB
            'id_tipo_socio' => 2, // Técnico
            'id_colegio' => null, // Aplica a todos
            'monto' => 17.40,
        ]);

        // ==========================================
        // 2. APORTE COLEGIO (Específico por Colegio)
        // ==========================================

        // Obtenemos todos los colegios registrados
        $colegios = Colegio::all();

        foreach ($colegios as $colegio) {
            // Aporte Mensual para Ingenieros en este colegio
            Tarifa::create([
                'id_concepto' => 2, // Aporte Colegio
                'id_tipo_socio' => 1, // Ingeniero
                'id_colegio' => $colegio->id,
                'monto' => 30.00, // Monto estándar (ajustar si cada colegio cobra diferente)
            ]);

            // Aporte Mensual para Técnicos en este colegio
            Tarifa::create([
                'id_concepto' => 2, // Aporte Colegio
                'id_tipo_socio' => 2, // Técnico
                'id_colegio' => $colegio->id,
                'monto' => 10.00, // Monto estándar (ajustar si varía)
            ]);
        }

        // ==========================================
        // 3. OTROS CONCEPTOS (No periódicos)
        // ==========================================

        // --- MATRÍCULAS ---
        Tarifa::create(['id_concepto' => 3, 'id_tipo_socio' => 1, 'id_colegio' => null, 'monto' => 1113.60]);
        Tarifa::create(['id_concepto' => 3, 'id_tipo_socio' => 2, 'id_colegio' => null, 'monto' => 626.40]);

        // --- CERTIFICADOS ---
        Tarifa::create(['id_concepto' => 4, 'id_tipo_socio' => 1, 'id_colegio' => null, 'monto' => 35.00]);
        Tarifa::create(['id_concepto' => 4, 'id_tipo_socio' => 2, 'id_colegio' => null, 'monto' => 35.00]);
    }
}
