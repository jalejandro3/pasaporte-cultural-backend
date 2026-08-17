<?php

namespace App\Application\Participation;

use App\Application\Activity\NotFoundActivityException;
use App\Application\User\NotFoundUserException;
use App\Domain\Activity\ActivityRepository;
use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationRepository;
use App\Domain\User\UserRepository;

readonly class CreateParticipation
{
    public function __construct(
        private ParticipationRepository $participationRepository,
        private ActivityRepository $activityRepository,
        private UserRepository $userRepository
    ) {}

    /**
     * @throws ParticipationExistsException
     * @throws ParticipationVerificationCodeMismatchException
     * @throws NotFoundActivityException
     * @throws NotFoundUserException
     */
    public function execute(string $assistantId, string $activityId, \DateTimeImmutable $startTime, ?string $verificationCode): Participation
    {
        $activity = $this->activityRepository->findById($activityId);

        if (!$activity) {
            throw new NotFoundActivityException('Activity not found.');
        }

        if ($activity->getVerificationCode() !== $verificationCode) {
            throw new ParticipationVerificationCodeMismatchException('Invalid verification code provided.');
        }

        $assistant = $this->userRepository->findById($assistantId);

        if (!$assistant) {
            throw new NotFoundUserException('Assistant not found.');
        }

        $currentParticipation = $this->participationRepository->findByActivityIdAndAssistantId($activityId, $assistantId);

        if ($currentParticipation) {
            throw new ParticipationExistsException('Assistant already participated in this activity.');
        }

        $newParticipation = Participation::create($assistantId, $activityId, $activity->getTotalHours(), $startTime);

        $this->participationRepository->save($newParticipation);

        return $newParticipation;
    }
}
