<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\Paginator;

interface UserService
{
    public function getProfile(string $token): array;
    public function updateProfile(string $token, array $data): array;
    public function updateRole(int $id, array $data): array;
    public function getAllUsers(array $filters, int $perPage, string $sortBy, string $sortOrder): Paginator;
}
