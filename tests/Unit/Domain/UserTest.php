<?php

namespace Tests\Unit\Domain;

use App\Domain\User\InvalidEmailFormatException;
use App\Domain\User\User;
use App\Domain\User\UserRole;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_user_creation_with_invalid_email_format_throws_exception()
    {
        $this->expectException(InvalidEmailFormatException::class);
        new User(
            'John',
            'Doe',
            '1234567890',
            'password',
            'email',
            UserRole::ASSISTANT
        );
    }
}
