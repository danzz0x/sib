<?php

namespace App\Enums;

enum TipoContacto: string {
    case Email = 'email';
    case Telefono = 'telefono';
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case Twitter = 'twitter';
    case Youtube = 'youtube';
    case Otro = 'otro';

    public function icon(): string {
        return match($this) {
            self::Email => 'ri-mail-line',
            self::Telefono => 'ri-phone-line',
            self::Facebook => 'ri-facebook-fill',
            self::Instagram => 'ri-instagram-line',
            self::Twitter => 'ri-twitter-fill',
            self::Youtube => 'ri-youtube-fill',
            self::Otro => 'ri-link-m',
        };
    }
}
