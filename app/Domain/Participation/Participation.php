<?php

namespace App\Domain\Participation;

class Participation
{
    private ParticipationId $id;
    private RequiredHours $requiredHours;
    private \DateTimeImmutable|null $endTime = null;

    private function __construct(
        private readonly string $assistantId,
        private readonly string $activityId,
        int $activityTotalHours,
        private readonly \DateTimeImmutable $startTime,
    ) {
        $this->requiredHours = new RequiredHours($activityTotalHours);
    }

    public static function create(string $assistantId, string $activityId, int $activityTotalHours, \DateTimeImmutable $startTime): self
    {
        $participation = new Participation($assistantId, $activityId, $activityTotalHours, $startTime);
        $participation->id = ParticipationId::generate();

        return $participation;
    }

    public static function fromDatabase(string $id, string $assistantId, string $activityId, int $activityTotalHours, \DateTimeImmutable $startTime, \DateTimeImmutable|null $endTime): self
    {
        $participation = new Participation($assistantId, $activityId, $activityTotalHours, $startTime);

        if ($endTime) {
            $participation->endTime = $endTime;
        }

        $participation->id = ParticipationId::fromString($id);

        return $participation;
    }

    public function getId(): ParticipationId
    {
        return $this->id;
    }

    public function getAssistantId(): string
    {
        return $this->assistantId;
    }

    public function getActivityId(): string
    {
        return $this->activityId;
    }

    public function getStartTime(): \DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): \DateTimeImmutable|null
    {
        return $this->endTime;
    }

    public function status(): ParticipationStatus
    {
        if ($this->endTime) {
            $participationHours = ($this->endTime->getTimestamp() - $this->startTime->getTimestamp()) / 3600;

            if ($this->requiredHours->isSatisfiedBy($participationHours)) {
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
