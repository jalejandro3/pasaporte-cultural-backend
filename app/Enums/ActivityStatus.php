<?php

namespace App\Enums;

enum ActivityStatus:string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case NOT_COMPLETED = 'not_completed';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'En Progreso',
            self::COMPLETED => 'Completado',
            self::NOT_COMPLETED => 'No Completado',
        };
    }
}
