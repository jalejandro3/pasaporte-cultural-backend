<?php

namespace App\Repositories\Impl;

use App\Models\User;
use App\Repositories\UserRepository as UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\Paginator;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly User $user)
    {
    }

    public function create(array $data): User
    {
        return tap(new User($data), function ($user) {
            $user->save();
        });
    }

    public function findByActivity(int $activityId): Collection
    {
        $query = $this->user->query();

        return $query
            ->join('user_activity', 'users.id', '=', 'user_activity.user_id')
            ->where('user_activity.activity_id', $activityId)
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'user_activity.status'
            )
            ->get();
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

    public function findBySearchTerm(?string $search): Builder|Model
    {
        $query = $this->user->query();

        $query->where('email', 'like', "%$search%")
            ->orWhere('id_document', 'like', "%$search%");

        return $query->first();
    }
}
