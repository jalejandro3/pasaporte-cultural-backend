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

    public function findEnrolledByUser(int $perPage, int $userId): Paginator
    {
        $query = $this->activity->query();

        return $query->join('user_activity', 'activities.id', '=', 'user_activity.activity_id')
            ->where('user_activity.user_id', $userId)
            ->select('activities.id', 'activities.title', 'activities.duration', 'user_activity.status')
            ->paginate($perPage);
    }
}
