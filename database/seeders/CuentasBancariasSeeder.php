<?php

namespace Database\Seeders;

use App\Models\CuentaBancaria;
use Illuminate\Database\Seeder;

class CuentasBancariasSeeder extends Seeder
{
    public function run(): void
    {
        // Cuenta SIB Nacional (Para aportes SIB)
        CuentaBancaria::create([
            'id_colegio' => null,
            'banco' => 'Banco Unión',
            'nro_cuenta' => '100000123456',
            'titular' => 'Sociedad de Ingenieros de Bolivia',
            'qr_path' => 'qrs/sib_nacional.jpg',
            'activo' => true,
        ]);

        // Cuenta Colegio Civil
        CuentaBancaria::create([
            'id_colegio' => 1,
            'banco' => 'Banco Nacional de Bolivia',
            'nro_cuenta' => '2000-55555',
            'titular' => 'Colegio de Ing. Civiles',
            'qr_path' => 'qrs/civil.jpg',
            'activo' => true,
        ]);
    }
}
