<?php

namespace App\Enums;

enum EspecialidadEnum: string
{
    // === CISTRIP POTOSÍ (Sistemas) ===
    case SISTEMAS = 'Ingeniero de Sistemas';
    case INFORMATICA = 'Ingeniero Informático';
    case TELECOMUNICACIONES = 'Ingeniero de Telecomunicaciones';

    // === CIC-POTOSÍ (Civil) ===
    case CIVIL = 'Ingeniero Civil';

    // === CIAP-POTOSÍ (Ambiental) ===
    case AMBIENTAL = 'Ingeniero Ambiental';

    // === CIEE-POTOSÍ (Eléctrica) ===
    case ELECTRICA = 'Ingeniero Eléctrico';
    case ELECTRONICA = 'Ingeniero Electrónico';

    // === CIM POTOSÍ (Mecánica) ===
    case MECANICA = 'Ingeniero Mecánico';
    case MECATRONICA = 'Ingeniero Mecatrónico';

    // === CICAP-POTOSÍ (Agroalimentaria) ===
    case AGRONOMIA = 'Ingeniero Agrónomo';

    // === TÉCNICOS (Nuevos agregados) ===
    case TECNICO_AMBIENTAL = 'Técnico en Medio Ambiente';
    case TECNICO_CONSTRUCCION = 'Técnico en Construcción Civil';
    case TECNICO_SISTEMAS = 'Técnico Superior en Sistemas';
    case TECNICO_ELECTRICISTA = 'Técnico Electricista';
    case TECNICO_AGRONOMO = 'Técnico Agrónomo';
    case TECNICO_MECANICO = 'Técnico Mecánico';

    // Método helper
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
