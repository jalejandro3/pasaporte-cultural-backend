<?php

namespace App\Repositories\Impl;

use App\Models\Activity;
use App\Repositories\ActivityRepository as ActivityRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;

class ActivityRepository implements ActivityRepositoryInterface
{
    public function __construct(private readonly Activity $activity)
    {
    }

    public function create(array $data): Activity
    {
        return tap(new Activity($data), function ($activity) {
            $activity->save();
        });
    }

    public function findById(string $id): ?Activity
    {
        return $this->activity->find($id);
    }

    public function findByFilters(array $filters, int $perPage, string $sortBy, string $sortOrder): Paginator
    {
        $query = $this->activity->query();

        foreach ($filters as $key => $value) {
            $query->where($key, 'like', "%$value%");
        }

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }
}
