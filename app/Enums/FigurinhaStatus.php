<?php

namespace App\Enums;

enum FigurinhaStatus: string
{
    case Pendente = 'pendente';
    case Aprovado = 'aprovado';
    case Rejeitado = 'rejeitado';

    public function label(): string
    {
        return match($this) {
            self::Pendente => 'Pendente',
            self::Aprovado => 'Aprovado',
            self::Rejeitado => 'Rejeitado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendente => 'text-amber-600 bg-amber-50',
            self::Aprovado => 'text-green-600 bg-green-50',
            self::Rejeitado => 'text-red-600 bg-red-50',
        };
    }
}
