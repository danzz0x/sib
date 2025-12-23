<?php

namespace Database\Seeders;

use App\Enums\EspecialidadEnum;
use App\Models\Colegio;
use App\Models\Socio;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SociosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener IDs de colegios existentes
        $colegioCivil = Colegio::where('nombre', 'like', '%Civil%')->first();
        $colegioSistemas = Colegio::where('nombre', 'like', '%Sistemas%')->first();
        $colegioAmbiental = Colegio::where('nombre', 'like', '%Ambiental%')->first();

        // Fallback seguro
        $idCivil = $colegioCivil?->id ?? 1;
        $idSistemas = $colegioSistemas?->id ?? 1;
        $idAmbiental = $colegioAmbiental?->id ?? 1;

        // 2. Crear Socios

        // Socio 1: Ingeniero Civil
        Socio::create([
            'id_tipo_socio' => 1, // Ingeniero
            'id_colegio' => $idCivil,
            'nombre' => 'Juan Pérez Mamani',
            'cedula' => '1234567 PT',
            'rni' => '10500',
            'especialidad' => EspecialidadEnum::CIVIL,
            'email' => 'juan.perez@email.com',
            'telefono' => '71020304',
            'fecha_registro' => Carbon::parse('2023-01-15'),
            'estado' => 'Activo',
        ]);

        // Socio 2: Ingeniero de Sistemas
        Socio::create([
            'id_tipo_socio' => 1, // Ingeniero
            'id_colegio' => $idSistemas,
            'nombre' => 'Maria Rodriguez Lopez',
            'cedula' => '8901234 LP',
            'rni' => '11200',
            'especialidad' => EspecialidadEnum::SISTEMAS,
            'email' => 'maria.rod@email.com',
            'telefono' => '69050403',
            'fecha_registro' => Carbon::parse('2023-05-20'),
            'estado' => 'Activo',
        ]);

        // Socio 3: Técnico Ambiental (CORREGIDO)
        Socio::create([
            'id_tipo_socio' => 2, // Técnico
            'id_colegio' => $idAmbiental,
            'nombre' => 'Carlos Condori Quispe',
            'cedula' => '4567890 OR',
            'rni' => '5500',
            // Ahora usamos la especialidad correcta para técnico
            'especialidad' => EspecialidadEnum::TECNICO_AMBIENTAL,
            'email' => 'carlos.condori@email.com',
            'telefono' => '77788990',
            'fecha_registro' => Carbon::parse('2024-02-10'),
            'estado' => 'Pendiente',
        ]);
    }
}
