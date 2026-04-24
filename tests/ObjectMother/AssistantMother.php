<?php

namespace Tests\ObjectMother;

use App\Domain\User\User;
use App\Domain\User\UserRole;

class AssistantMother
{
    public static function create(string $email = 'assistant@example.com'): User
    {
        return new User(
            'John',
            'Doe',
            '1234567890',
            'password',
            $email,
            UserRole::ASSISTANT
        );
    }
}
