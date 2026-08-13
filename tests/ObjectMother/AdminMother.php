<?php

namespace Tests\ObjectMother;

use App\Domain\User\User;
use App\Domain\User\UserRole;

class AdminMother
{
    public static function create(): User
    {
        return User::create(
            'admin',
            'admin',
            '0000000000',
            'admin@example.com',
            UserRole::ADMIN
        );
    }
}
