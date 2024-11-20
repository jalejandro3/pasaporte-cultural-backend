<?php

namespace App\Services\Impl;

use App\Services\UserService as UserServiceInterface;

class UserService implements UserServiceInterface
{
    public function getProfile(string $token): array
    {
        $user = jwt_decode_token($token);

        return (array) $user->data;
    }
}
