<?php

namespace App\Domain\Activity;

interface ActivityRepository
{
    public function findById(string $id): ?Activity;
}
