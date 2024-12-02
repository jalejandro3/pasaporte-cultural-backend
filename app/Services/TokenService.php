<?php

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Models\User;

interface TokenService
{
    public function createAccessToken(User $user): string;
    public function createRefreshToken(User $user): string;
    public function refresh(string $refreshToken): array;
}
