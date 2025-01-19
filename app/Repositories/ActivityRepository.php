<?php

namespace App\Repositories;

use App\Models\Activity;

interface ActivityRepository
{
    public function create(array $data): Activity;
    public function findById(string $id): ?Activity;
}
