<?php

namespace Tests\Feature;

use App\Domain\User\UserRole;
use App\Infrastructure\User\EloquentUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ObjectMother\AssistantMother;
use Tests\TestCase;

class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_user()
    {
        $assistant = AssistantMother::create();
        $password = 'password';
        $userRepository = new EloquentUserRepository();

        $userRepository->save($assistant, $password);

        $storedUser = \DB::table('users')->find($assistant->getId());

        $this->assertDatabaseHas('users', [
            'first_name' => $assistant->getFirstName(),
            'last_name' => $assistant->getLastName(),
            'id_document' => $assistant->getIdDocument(),
            'email' => $assistant->getEmail(),
            'role' => $assistant->getRole()->value
        ]);

        $this->assertTrue(\Hash::check($password, $storedUser->password));
    }

    public function test_update_user()
    {
        $assistant = AssistantMother::create();
        $password = 'password';
        $userRepository = new EloquentUserRepository();

        $userRepository->save($assistant, $password);
        $assistant->setRole(UserRole::ADMIN);
        $userRepository->update($assistant);

        $storedUser = \DB::table('users')->find($assistant->getId());

        $this->assertDatabaseHas('users', [
            'id' => $assistant->getId(),
            'role' => UserRole::ADMIN->value
        ]);

        $this->assertTrue(\Hash::check($password, $storedUser->password));
    }
}
