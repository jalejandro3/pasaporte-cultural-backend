<?php

namespace App\Repositories\Impl;

use App\Models\User;
use App\Repositories\UserRepository as UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly User $user)
    {
    }

    public function create(array $data): bool
    {
        return $this->user->fill($data)->save();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user->whereEmail($email)->first();
    }

    public function findByIdDocument(string $idDocument): ?User
    {
        return $this->user->whereIdDocument($idDocument)->first();
    }
}
