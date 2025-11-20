<?php

namespace App\Enums;

enum TipoMostrarSeccion: string
{
    case Carrusel = 'carrusel';
    case Cuadricula = 'cuadricula';
    case Lista = 'lista';
    case Directorio = 'directorio';

    /**
     * Obtener el icono correspondiente para cada tipo
     */
    public function icon(): string
    {
        return match ($this) {
            self::Carrusel => 'ri-slideshow-3-line',
            self::Cuadricula => 'ri-layout-grid-line',
            self::Lista => 'ri-list-unordered',
            self::Directorio => 'ri-team-line',
        };
    }

    /**
     * Obtener la descripción del tipo
     */
    public function descripcion(): string
    {
        return match ($this) {
            self::Carrusel => 'Desplazamiento horizontal de publicaciones',
            self::Cuadricula => 'Disposición en rejilla (grid)',
            self::Lista => 'Disposición vertical en lista',
            self::Directorio => 'Listado de miembros del directorio',
        };
    }

    /**
     * Obtener el label para mostrar
     */
    public function label(): string
    {
        return match ($this) {
            self::Carrusel => 'Carrusel',
            self::Cuadricula => 'Cuadrícula',
            self::Lista => 'Lista',
            self::Directorio => 'Directorio',
        };
    }
}

