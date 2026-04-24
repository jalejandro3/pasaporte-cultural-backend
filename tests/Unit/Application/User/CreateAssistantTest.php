<?php

namespace Tests\Unit\Application\User;

use App\Application\User\UserDTO;
use App\Application\User\CreateAssistant;
use App\Application\User\InvalidEmailDomainException;
use App\Application\User\UserExistsException;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserRole;
use PHPUnit\Framework\TestCase;

class CreateAssistantTest extends TestCase
{
    public function test_create_assistant_with_valid_email_domain()
    {
        $userRepository = $this->createMock(UserRepository::class);

        $userRepository->method('findByEmail')->willReturn(null);
        $userRepository->expects($this->once())->method('save');

        $assistantDto = UserDTO::fromArray([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'id_document' => '123456789',
            'password' => 'password123',
            'email' => 'assistant@unir.net'
        ]);

        $createAssistant = new CreateAssistant($userRepository);
        $newAssistant = $createAssistant->execute($assistantDto);

        $this->assertSame('assistant@unir.net', $newAssistant->getEmail());
    }

    public function test_create_assistant_with_invalid_email_domain()
    {
        $userRepository = $this->createStub(UserRepository::class);
        $assistantDto = UserDTO::fromArray([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'id_document' => '123456789',
            'password' => 'password123',
            'email' => 'assistant@gmail.com'
        ]);

        $createAssistant = new CreateAssistant($userRepository);

        $this->expectException(InvalidEmailDomainException::class);
        $this->expectExceptionMessage('Invalid email domain.');

        $createAssistant->execute($assistantDto);
    }

    public function test_create_existing_email_assistant_throws_exception()
    {
        $assistantData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'id_document' => '123456789',
            'password' => 'password',
            'email' => 'assistant@unir.net'
        ];

        $existingAssistant = new User(
            $assistantData['first_name'],
            $assistantData['last_name'],
            $assistantData['id_document'],
            $assistantData['password'],
            $assistantData['email'],
            UserRole::ASSISTANT
        );

        $userRepository = $this->createStub(UserRepository::class);

        $userRepository->method('findByEmail')->willReturn($existingAssistant);

        $assistantDto = UserDTO::fromArray($assistantData);

        $createAssistant = new CreateAssistant($userRepository);

        $this->expectException(UserExistsException::class);
        $this->expectExceptionMessage('Assistant with this email already exists.');

        $createAssistant->execute($assistantDto);
    }

    public function test_create_existing_id_document_assistant_throws_exception()
    {
        $assistantData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'id_document' => '123456789',
            'password' => 'password',
            'email' => 'assistant@unir.net',
        ];

        $existingAssistant = new User(
            $assistantData['first_name'],
            $assistantData['last_name'],
            $assistantData['id_document'],
            $assistantData['password'],
            $assistantData['email'],
            UserRole::ASSISTANT
        );

        $userRepository = $this->createStub(UserRepository::class);

        $userRepository->method('findByIdDocument')->willReturn($existingAssistant);

        $assistantDto = UserDTO::fromArray($assistantData);

        $createAssistant = new CreateAssistant($userRepository);

        $this->expectException(UserExistsException::class);
        $this->expectExceptionMessage('Assistant with this id document already exists.');

        $createAssistant->execute($assistantDto);
    }
}
