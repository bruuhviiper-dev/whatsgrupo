<?php

namespace App\Enums;

enum FigurinhaCategoria: string
{
    case Engracado = 'Engracado';
    case Amor = 'Amor';
    case Raiva = 'Raiva';
    case Triste = 'Triste';
    case Surpresa = 'Surpresa';
    case Motivacao = 'Motivacao';
    case Zoeira = 'Zoeira';
    case Outros = 'Outros';

    public function label(): string
    {
        return match($this) {
            self::Engracado => 'Engraçado',
            self::Amor => 'Amor',
            self::Raiva => 'Raiva',
            self::Triste => 'Triste',
            self::Surpresa => 'Surpresa',
            self::Motivacao => 'Motivação',
            self::Zoeira => 'Zoeira',
            self::Outros => 'Outros',
        };
    }

    public function emoji(): string
    {
        return match($this) {
            self::Engracado => '😂',
            self::Amor => '❤️',
            self::Raiva => '😡',
            self::Triste => '😢',
            self::Surpresa => '😲',
            self::Motivacao => '💪',
            self::Zoeira => '🤪',
            self::Outros => '📦',
        };
    }
}
