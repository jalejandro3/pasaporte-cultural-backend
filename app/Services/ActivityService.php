<?php

namespace App\Services;

interface ActivityService
{
    public function create(array $data): array;
    public function show(string $token, string $id): array;
}
