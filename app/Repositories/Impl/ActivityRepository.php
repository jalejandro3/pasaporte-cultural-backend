<?php

namespace App\Repositories\Impl;

use App\Models\Activity;
use App\Repositories\ActivityRepository as ActivityRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

    public function findByUser(int $userId): Collection
    {
        $query = $this->activity->query();

        return $query
            ->join('user_activity', 'activities.id', '=', 'user_activity.activity_id')
            ->where('user_activity.user_id', $userId)
            ->select(
                'activities.id',
                'activities.title',
                'activities.duration',
                'user_activity.status'
            )
            ->get();
    }

    public function findById(int $id): ?Activity
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

    public function findBySearchTerm(?string $search): Builder|Model
    {
        $query = $this->activity->query();

        $query->where('title', 'like', "%$search%");

        return $query->first();
    }

    public function findByQuery(?string $q): Collection
    {
        $query = $this->activity->query();

        return $query->where('title', 'like', "%$q%")
            ->select('id', 'title')
            ->get();
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
