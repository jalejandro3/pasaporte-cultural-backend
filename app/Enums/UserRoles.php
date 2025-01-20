<?php

namespace App\Enums;

enum UserRoles
{
    public const ADMIN = 'admin';
    public const ASSISTANT = 'assistant';

    public static function getValues(): array
    {
        return [
            self::ADMIN,
            self::ASSISTANT
        ];
    }
}
