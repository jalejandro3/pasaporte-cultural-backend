<?php

namespace App\Application\Participation;

use App\Domain\Activity\Activity;
use App\Domain\Participation\FinishedParticipationException;
use App\Domain\Participation\NotFoundParticipationException;
use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationRepository;
use App\Domain\Participation\ParticipationVerificationCodeMismatchException;
use App\Domain\Participation\PriorEndDateParticipationException;
use App\Domain\User\User;

readonly class FinishParticipation
{
    public function __construct(private ParticipationRepository $participationRepository) {}

    /**
     * @throws ParticipationVerificationCodeMismatchException
     * @throws FinishedParticipationException
     * @throws PriorEndDateParticipationException
     * @throws NotFoundParticipationException
     */
    public function execute(User $assistant, Activity $activity, \DateTimeImmutable $endTime, ?string $verificationCode): Participation
    {
        if ($activity->getVerificationCode() !== $verificationCode) {
            throw new ParticipationVerificationCodeMismatchException('Invalid verification code provided.');
        }

        $currentParticipation = $this->participationRepository
            ->findByActivityIdAndAssistantId($activity->getId(), $assistant->getId());

        if (!$currentParticipation) {
            throw new NotFoundParticipationException('Participation not found.');
        }

        $currentParticipation->finish($endTime);

        $this->participationRepository->save($currentParticipation);

        return $currentParticipation;
    }
}
