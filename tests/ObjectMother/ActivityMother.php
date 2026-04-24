<?php

namespace Tests\ObjectMother;

use App\Domain\Activity\Activity;

class ActivityMother
{
    public static function create(int $totalHours = 2): Activity
    {
        return new Activity(
            1,
            'Activity Title',
            'Activity Description',
            'United States',
            'Buffalo', '110 Fairview Road',
            $totalHours
        );
    }
}
