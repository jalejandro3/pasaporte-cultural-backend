<?php

namespace App\Repositories\Impl;

use App\Models\User;
use App\Repositories\UserRepository as UserRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly User $user)
    {
    }

    public function create(array $data): bool
    {
        return $this->user->fill($data)->save();
    }

    public function findById(int $id): ?User
    {
        return $this->user->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user->whereEmail($email)->first();
    }

    public function findByIdDocument(string $idDocument): ?User
    {
        return $this->user->whereIdDocument($idDocument)->first();
    }

    public function findByFilters(array $filters, int $perPage, string $sortBy, string $sortOrder): Paginator
    {
        $query = $this->user->query();

        foreach ($filters as $key => $value) {
            $query->where($key, 'like', "%$value%");
        }

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }
}
