<?php

namespace App\Repositories;

use App\Models\Activity;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ActivityRepository
{
    public function create(array $data): Activity;
    public function findByUser(int $userId): Collection;
    public function findById(int $id): ?Activity;
    public function findByFilters(array $filters, int $perPage, string $sortBy, string $sortOrder): Paginator;
    public function findBySearchTerm(?string $search): Builder|Model;
    public function findEnrolledByUser(int $perPage, int $userId): Paginator;
}
