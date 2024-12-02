<?php

namespace App\Services;

use App\Exceptions\ApplicationException;

interface AuthService
{
    /**
     * @throws ApplicationException
     */
    public function login(string $email, string $password): array;

    /**
     * @throws ApplicationException
     */
    public function register(array $userData): array;
}
