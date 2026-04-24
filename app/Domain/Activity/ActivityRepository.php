<?php

namespace App\Domain\Activity;

interface ActivityRepository
{
    public function findById(int $id): ?Activity;
}
