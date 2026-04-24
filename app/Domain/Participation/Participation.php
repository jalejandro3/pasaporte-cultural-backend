<?php

namespace App\Domain\Participation;

use App\Domain\Activity\Activity;
use App\Domain\User\User;

class Participation
{
    private \DateTimeImmutable|null $endTime = null;

    public function __construct(
        private readonly User $assistant,
        private readonly Activity $activity,
        private readonly \DateTimeImmutable $startTime,
    ) {}

    public function status(): ParticipationStatus
    {
        if ($this->endTime) {
            $participationHours = ($this->endTime->getTimestamp() - $this->startTime->getTimestamp()) / 3600;

            if ($participationHours >= $this->activity->getTotalHours()) {
                return ParticipationStatus::COMPLETED;
            }

            return ParticipationStatus::NOT_COMPLETED;
        }

        return ParticipationStatus::IN_PROCESS;
    }

    /**
     * @throws FinishedParticipationException
     * @throws PriorEndDateParticipationException
     */
    public function finish(\DateTimeImmutable $endTime): void
    {
        if ($endTime < $this->startTime) {
            throw new PriorEndDateParticipationException('The end time cannot be before the start time.');
        }

        if ($this->status() !== ParticipationStatus::IN_PROCESS) {
            throw new FinishedParticipationException('Participation is already finished.');
        }

        $this->endTime = $endTime;
    }
}
