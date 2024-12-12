<?php

namespace App\Services\Impl;

use App\Services\UserService as UserServiceInterface;
use App\Repositories\UserRepository as UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}
    public function getProfile(string $token): array
    {
        $user = jwt_decode_token($token);

        return (array) $user->data;
    }
    public function updateProfile(string $token, array $data): void
    {
        // Decodificar el token para obtener los datos del usuario
        $data = jwt_decode_token($token);
        $user = $this->userRepository->findById($data->data->id);
        if (!$user) {
            throw new ResourceNotFoundException('User does not exists.');
        }
        $user->update([
            'email' => $user['email'],
            'password' => Hash::make($user['password']),
        ]);
    }
}
