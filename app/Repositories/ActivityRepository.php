<?php

namespace App\Repositories;

use App\Models\Activity;
use Illuminate\Contracts\Pagination\Paginator;

interface ActivityRepository
{
    public function create(array $data): Activity;
    public function findById(string $id): ?Activity;
    public function findByFilters(array $filters, int $perPage, string $sortBy, string $sortOrder): Paginator;
    public function findEnrolledByUser(int $perPage, int $userId): Paginator;
}
