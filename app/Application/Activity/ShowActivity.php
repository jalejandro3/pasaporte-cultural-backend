<?php

namespace App\Application\Activity;

use App\Domain\Activity\ActivityRepository;
use App\Domain\User\User;
use App\Domain\User\UserRole;

readonly class ShowActivity
{
    public function __construct(private ActivityRepository $activityRepository) {}

    public function execute(User $user, int $activityId): ActivityDTO
    {
        $activity = $this->activityRepository->findById($activityId);
        $verificationCode = (UserRole::ADMIN === $user->getRole()) ? $activity->getVerificationCode() : null;

        return ActivityDTO::fromEntity($activity, $verificationCode);
    }
}
