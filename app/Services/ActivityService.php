<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\Paginator;

interface ActivityService
{
    public function create(array $data): array;
    public function getAllActivities(array $filters, int $perPage, string $sortBy, string $sortOrder): Paginator;
    public function show(string $token, string $id): array;
    public function register(int $activityId, string $token): array;
}
