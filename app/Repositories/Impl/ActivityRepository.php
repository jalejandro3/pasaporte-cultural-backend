<?php

namespace App\Repositories\Impl;

use App\Models\Activity;
use App\Repositories\ActivityRepository as ActivityRepositoryInterface;

class ActivityRepository implements ActivityRepositoryInterface
{
    public function create(array $data): Activity
    {
        return tap(new Activity($data), function ($activity) {
            $activity->save();
        });
    }

    public function findById(string $id): ?Activity
    {
        return Activity::find($id);
    }
}
