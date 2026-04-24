<?php

namespace App\Application\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserRole;

readonly class CreateAssistant
{
    public function __construct(private UserRepository $userRepository) {}

    public function execute(UserDTO $assistantDTO): User
    {
        $domain = explode('@', $assistantDTO->email)[1];

        if ('unir.net' !== $domain) {
            throw new InvalidEmailDomainException('Invalid email domain.');
        }

        $existingAssistant = $this->userRepository->findByEmail($assistantDTO->email);

        if ($existingAssistant) {
            throw new UserExistsException('Assistant with this email already exists.');
        }

        $existingAssistant = $this->userRepository->findByIdDocument($assistantDTO->idDocument);

        if ($existingAssistant) {
            throw new UserExistsException('Assistant with this id document already exists.');
        }

        $assistant = new User(
            $assistantDTO->firstName,
            $assistantDTO->lastName,
            $assistantDTO->idDocument,
            $assistantDTO->password,
            $assistantDTO->email,
            UserRole::ASSISTANT
        );

        $this->userRepository->save($assistant);

        return $assistant;
    }
}
