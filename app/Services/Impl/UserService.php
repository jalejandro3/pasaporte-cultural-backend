<?php

namespace App\Services\Impl;

use App\Services\UserService as UserServiceInterface;
use App\Repositories\UserRepository as UserRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UserService implements UserServiceInterface
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function getProfile(string $token): array
    {
        $user = jwt_decode_token($token);

        return (array) $user->data;
    }

    public function getAllUsers(array $filters, int $perPage, string $sortBy, string $sortOrder): Paginator
    {
        return $this->userRepository->findByFilters($filters, $perPage, $sortBy, $sortOrder);
    }

    public function updateProfile(string $token, array $data): array
    {
        $decoded = jwt_decode_token($token);
        $user = $this->userRepository->findById($decoded->data->id);

        if (!$user) {
            throw new ResourceNotFoundException('User not found.');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return ['message' => 'User updated successfully.'];
    }

    public function updateRole(int $id, array $data): array
    {
        if (!$user = $this->userRepository->findById($id)) {
            throw new ResourceNotFoundException('User not found.');
        }

        $role = $data['role'];

        if ($role !== $user->role) {
            $user->role = $role;

            $user->save();
        }

        return ['message' => 'User updated successfully.'];
    }
}
