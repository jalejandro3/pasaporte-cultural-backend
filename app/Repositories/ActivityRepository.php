<?php

namespace App\Repositories;

use App\Models\Activity;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

interface ActivityRepository
{
    public function create(array $data): Activity;
    public function findByUser(int $userId): Collection;
    public function findById(int $id): ?Activity;
    public function findByFilters(array $filters, int $perPage, string $sortBy, string $sortOrder): Paginator;
    public function findEnrolledByUser(int $perPage, int $userId): Paginator;
}
