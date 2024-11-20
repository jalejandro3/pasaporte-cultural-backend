<?php

namespace App\Services\Impl;

use App\Exceptions\ApplicationException;
use App\Repositories\UserRepository as UserRepositoryInterface;
use App\Services\AuthService as AuthServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class AuthService implements AuthServiceInterface
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new ResourceNotFoundException('User does not exists.');
        }

        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            throw new ApplicationException('Wrong email or password, please verify your data.');
        }

        return [
            'token' => jwt_build_token($user->toArray()),
            'data' => $user->toArray()
        ];
    }


    public function register(array $userData): array
    {
        $this->emailExists($userData['email']);
        $this->idDocumentExists($userData['id_document']);

        $userData['password'] = Hash::make($userData['password']);

        $this->userRepository->create($userData);

        return ['message' => 'User created successfully.'];
    }

    /**
     * @throws ApplicationException
     */
    private function emailExists(string $email): void
    {
        if ($this->userRepository->findByEmail($email)) {
            throw new ApplicationException('The email is already taken, please use a new one.');
        }
    }

    /**
     * @throws ApplicationException
     */
    private function idDocumentExists(string $idDocument): void
    {
        if ($this->userRepository->findByIdDocument($idDocument)) {
            throw new ApplicationException('The id document is already exists.');
        }
    }
}
