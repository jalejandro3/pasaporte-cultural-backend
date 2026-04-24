<?php

namespace App\Application\User;

final readonly class UserDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $idDocument,
        public string $password,
        public string $email
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['first_name'],
            $data['last_name'],
            $data['id_document'],
            $data['password'],
            $data['email']
        );
    }
}
