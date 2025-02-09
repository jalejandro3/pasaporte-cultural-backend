<?php

namespace App\Enums;

enum UserRoles: string
{
    case ADMIN = 'admin';
    case ASSISTANT = 'assistant';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::ASSISTANT => 'Asistente',
        };
    }
}
