<?php

namespace Tests\Feature;

use App\Domain\User\UserRole;
use App\Infrastructure\User\EloquentUser;
use App\Infrastructure\User\EloquentUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_find_by_id()
    {
        $assistant = AssistantMother::create();
        $password = 'password';
        $userRepository = new EloquentUserRepository();

        $userRepository->save($assistant, $password);

        $foundUser = $userRepository->findById($assistant->getId());

        $this->assertEquals($assistant->getId(), $foundUser->getId());
        $this->assertEquals($assistant->getFirstName(), $foundUser->getFirstName());
        $this->assertEquals($assistant->getLastName(), $foundUser->getLastName());
        $this->assertEquals($assistant->getIdDocument(), $foundUser->getIdDocument());
        $this->assertEquals($assistant->getEmail(), $foundUser->getEmail());
        $this->assertEquals($assistant->getRole(), $foundUser->getRole());
    }
}
