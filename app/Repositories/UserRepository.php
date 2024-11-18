<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepository
{
    public function create(array $data): bool;
    public function findByEmail(string $email): ?User;
    public function findByIdDocument(string $idDocument): ?User;
}
