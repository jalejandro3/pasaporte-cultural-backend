<?php

namespace App\Services;

use App\Exceptions\ApplicationException;

interface AuthService
{
    /**
     * @throws ApplicationException
     */
    public function register(array $userData): array;
}
