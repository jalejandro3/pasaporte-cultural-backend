<?php

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Models\PasswordResetToken;

interface AuthService
{
    /**
     * @throws ApplicationException
     */
    public function forgotPassword(string $email): array;

    /**
     * @throws ApplicationException
     */
    public function login(string $email, string $password): array;

    /**
     * @throws ApplicationException
     */
    public function register(array $userData): array;

    /**
     * @throws ApplicationException
     */
    public function resetPassword(string $token, string $newPassword): array;

    /**
     * @throws ApplicationException
     */
    public function validateToken(string $token): array;
}
