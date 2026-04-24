<?php

namespace App\Application\Participation;

use App\Domain\Activity\Activity;
use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationExistsException;
use App\Domain\Participation\ParticipationRepository;
use App\Domain\Participation\ParticipationVerificationCodeMismatchException;
use App\Domain\User\User;

readonly class CreateParticipation
{
    public function __construct(private ParticipationRepository $participationRepository) {}

    /**
     * @throws ParticipationExistsException
     * @throws ParticipationVerificationCodeMismatchException
     */
    public function execute(User $assistant, Activity $activity, \DateTimeImmutable $startTime, ?string $verificationCode): Participation
    {
        if ($activity->getVerificationCode() !== $verificationCode) {
            throw new ParticipationVerificationCodeMismatchException('Invalid verification code provided.');
        }

        $currentParticipation = $this->participationRepository->findByActivityIdAndAssistantId($activity->getId(), $assistant->getId());

        if ($currentParticipation) {
            throw new ParticipationExistsException('Assistant already participated in this activity.');
        }

        $newParticipation = new Participation($assistant, $activity, $startTime);

        $this->participationRepository->save($newParticipation);

        return $newParticipation;
    }
}
