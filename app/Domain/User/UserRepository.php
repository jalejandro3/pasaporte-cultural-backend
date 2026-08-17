<?php

namespace App\Domain\User;

interface UserRepository
{
    public function countAdmins(): int;
    public function findByEmail(string $email): ?User;
    public function findById(string $id): ?User;
    public function findByIdDocument(string $idDocument): ?User;
    public function save(User $user, string $password): void;
    public function update(User $user): void;
}
