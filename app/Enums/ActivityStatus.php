<?php

namespace App\Enums;

enum ActivityStatus
{
    public const IN_PROGRESS = 'in_progress';
    public const COMPLETED = 'completed';
    public const NOT_COMPLETED = 'not_completed';

    public static function getValues(): array
    {
        return [
            self::IN_PROGRESS,
            self::COMPLETED,
            self::NOT_COMPLETED
        ];
    }
}
