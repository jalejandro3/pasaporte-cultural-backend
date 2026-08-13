<?php

namespace App\Domain\User;

use Ramsey\Uuid\Uuid;

class User
{
    private string $id;

    private function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly string $idDocument,
        private readonly string $email,
        private UserRole $role
    )
    {
    }

    public static function create(string $firstName, string $lastName, string $idDocument, string $email, UserRole $role): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailFormatException();
        }

        $user = new User($firstName, $lastName, $idDocument, $email, $role);

        $user->generateId();

        return $user;
    }

    public static function fromDatabase(string $id, string $firstName, string $lastName, string $idDocument, string $email, UserRole $role): self
    {
        $user = new User($firstName, $lastName, $idDocument, $email, $role);
        $user->id = $id;

        return $user;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getIdDocument(): string
    {
        return $this->idDocument;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function setRole(UserRole $role): void
    {
        $this->role = $role;
    }

    public function isAdmin(): bool
    {
        return UserRole::ADMIN === $this->role;
    }

    private function generateId(): void
    {
        $this->id = Uuid::uuid4()->toString();
    }
}
