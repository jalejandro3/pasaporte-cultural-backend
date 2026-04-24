<?php

namespace App\Domain\User;

use Ramsey\Uuid\Uuid;

class User
{
    private string $id;

    public function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly string $idDocument,
        private readonly string $password,
        private readonly string $email,
        private readonly UserRole $role
    )
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailFormatException();
        }

        $this->generateId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    private function generateId(): void
    {
        $this->id = Uuid::uuid4()->toString();
    }
}
