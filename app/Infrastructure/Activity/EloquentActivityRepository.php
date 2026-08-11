<?php

namespace App\Infrastructure\Activity;

use App\Domain\Activity\Activity;
use App\Domain\Activity\ActivityRepository;

class EloquentActivityRepository implements ActivityRepository
{
    public function findById(string $id): ?Activity
    {
        $foundActivity = EloquentActivity::find($id);

        if (!$foundActivity) {
            return null;
        }

        return Activity::fromDatabase(
            $foundActivity->id,
            $foundActivity->title,
            $foundActivity->description,
            $foundActivity->country,
            $foundActivity->city,
            $foundActivity->address,
            $foundActivity->total_hours,
            $foundActivity->verification_code
        );
    }
}
