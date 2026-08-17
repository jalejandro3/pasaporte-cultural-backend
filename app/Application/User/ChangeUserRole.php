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
     * @throws NotFoundUserException
     */
    public function execute(User $actor, string $email, UserRole $role): void
    {
        if (!$actor->isAdmin()) {
            throw new AssignmentRoleException('You do not have permission to change the role of this user.');
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new NotFoundUserException('You cannot change role for non-existent user.');
        }

        if ($role === $user->getRole()) {
            return;
        }

        if ($role === UserRole::ASSISTANT && $email === $actor->getEmail() && $this->userRepository->countAdmins() === 1) {
            throw new CannotDemoteLastAdminException('You cannot demote the last admin user.');
        }

        $user->setRole($role);
        $this->userRepository->update($user);
    }
}
