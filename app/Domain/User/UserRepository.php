<?php

namespace App\Domain\User;

interface UserRepository
{
    public function findByEmail(string $email): ?User;
    public function findByIdDocument(string $idDocument): ?User;
    public function save(User $user): void;
}
