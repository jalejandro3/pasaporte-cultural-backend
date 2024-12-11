<?php

namespace App\Services\Impl;

use App\Services\UserService as UserServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    public function getProfile(string $token): array
    {
        $user = jwt_decode_token($token);

        return (array) $user->data;
    }
    public function updateProfile(string $token, array $data): void
    {
        // Decodificar el token para obtener los datos del usuario
        $user = jwt_decode_token($token);
        $userModel = User::findOrFail($user->data->id);
        $userModel->update([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
