<?php

namespace App\Services;

interface UserService
{
    public function getProfile(string $token): array;
    public function updateProfile(string $token, array $data): void;
}
