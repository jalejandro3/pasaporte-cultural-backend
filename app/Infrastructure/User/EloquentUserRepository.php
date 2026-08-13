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

    public function save(User $user, string $password): void
    {
        $userData = [
            'id' => $user->getId(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'id_document' => $user->getIdDocument(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()->value
        ];

        if ($password) {
            $userData['password'] = $password;
        }

        EloquentUser::create($userData);
    }

    public function update(User $user): void
    {
        EloquentUser::where('id', $user->getId())->update([
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'id_document' => $user->getIdDocument(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()->value
        ]);
    }
}
