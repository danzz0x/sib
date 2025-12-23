<?php

namespace Database\Seeders;

use App\Models\Colegio;
use App\Models\Concepto;
use App\Models\MetodoPago;
use App\Models\TipoSocio;
use Illuminate\Database\Seeder;

class CatalogosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoSocio::create(['nombre' => 'Ingeniero']);
        TipoSocio::create(['nombre' => 'Técnico']);

        Concepto::create(['nombre' => 'Aporte SIB', 'es_periodico' => true]);
        Concepto::create(['nombre' => 'Aporte Colegio', 'es_periodico' => true]);
        Concepto::create(['nombre' => 'Matrícula', 'es_periodico' => false]);
        Concepto::create(['nombre' => 'Certificado', 'es_periodico' => false]);

        MetodoPago::create(['nombre' => 'Efectivo']);
        MetodoPago::create(['nombre' => 'Transferencia QR']);
        MetodoPago::create(['nombre' => 'Depósito Bancario']);

        $colegios = [
            'Colegio De Ingenieros Ambientales De Potosí (CIAP-POTOSÍ)',
            'Colegio de Ingenieros Civiles (CIC-POTOSÍ)',
            'Colegio De Ingenieros En Ciencias Agroalimentarias Y Producción (CICAP-POTOSÍ)',
            'Colegio De Ingenieros Eléctricos y Electrónicos (CIEE-POTOSÍ)',
            'Colegio De Ingenieros Mecanicos (CIM-POTOSÍ)',
            'Colegio De Ingenieros De Sistemas, Telecomunicaciones, Redes E Informática Potosí (CISTRIP-POTOSÍ)',
        ];

        foreach ($colegios as $nombreColegio) {
            Colegio::create([
                'nombre' => $nombreColegio,
                'activo' => true,
            ]);
        }
    }
}
