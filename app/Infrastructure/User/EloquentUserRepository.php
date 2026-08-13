<?php

namespace App\Infrastructure\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository;

class EloquentUserRepository implements UserRepository
{
    public function countAdmins(): int
    {
        throw new \RuntimeException('Not implemented yet');
    }

    public function findByEmail(string $email): ?User
    {
        throw new \RuntimeException('Not implemented yet');
    }

    public function findByIdDocument(string $idDocument): ?User
    {
        throw new \RuntimeException('Not implemented yet');
    }

    public function save(User $user): void
    {
        throw new \RuntimeException('Not implemented yet');
    }
}
