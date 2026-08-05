<?php

namespace App\Application\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserRole;

readonly class ChangeUserRole
{
    public function __construct(private UserRepository $userRepository) {}

    /**
     * @throws AssignmentRoleException
     * @throws CannotDemoteLastAdminException
     */
    public function execute(User $admin, string $email, UserRole $role): void
    {
        if (!$admin->isAdmin()) {
            throw new AssignmentRoleException('You do not have permission to change the role of this user.');
        }

        if ($role === UserRole::ASSISTANT && $email === $admin->getEmail() && $this->userRepository->countAdmins() === 1) {
            throw new CannotDemoteLastAdminException('You cannot demote the last admin user.');
        }

        $user = $this->userRepository->findByEmail($email);

        $user->setRole($role);
        $this->userRepository->save($user);
    }
}
