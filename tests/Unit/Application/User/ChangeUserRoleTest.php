<?php

namespace Tests\Unit\Application\User;

use App\Application\User\AssignmentRoleException;
use App\Application\User\ChangeUserRole;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserRole;
use PHPUnit\Framework\TestCase;
use Tests\ObjectMother\AdminMother;
use Tests\ObjectMother\AssistantMother;

class ChangeUserRoleTest extends TestCase
{
    public function test_change_user_role_admin_user_change_assistant_to_admin_role()
    {
        $user = new User('John', 'Doe', '1234567890', 'password', 'user@unir.net', UserRole::ASSISTANT);
        $admin = AdminMother::create();
        $userRepository = $this->createMock(UserRepository::class);

        $userRepository->method('findByEmail')->willReturn($user);
        $userRepository->expects($this->once())->method('save');

        $changeUserRole = new ChangeUserRole($userRepository);

        $changeUserRole->execute($admin, $user->getEmail(), UserRole::ADMIN);

        $this->assertEquals(UserRole::ADMIN, $user->getRole());
    }

    public function test_change_user_role_assistant_user_without_change_role_permission_throws_exception()
    {
        $user = new User('John', 'Doe', '1234567890', 'password', 'user@unir.net', UserRole::ASSISTANT);
        $assistant = AssistantMother::create();

        $changeUserRole = new ChangeUserRole($this->createStub(UserRepository::class));

        $this->expectException(AssignmentRoleException::class);
        $this->expectExceptionMessage('You do not have permission to change the role of this user.');

        $changeUserRole->execute($assistant, $user->getEmail(), UserRole::ADMIN);
    }
}
