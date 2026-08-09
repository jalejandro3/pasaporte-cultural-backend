<?php

namespace App\Infrastructure\Participation;

use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationRepository;

class EloquentParticipationRepository implements ParticipationRepository
{
    public function findByActivityIdAndAssistantId(string $activityId, string $assistantId): ?Participation
    {
        throw new \RuntimeException('Not implemented yet');
    }

    public function save(Participation $participation): void
    {
        EloquentParticipation::create([
            'activity_id' => $participation->getActivity()->getId(),
            'assistant_id' => $participation->getAssistant()->getId(),
            'status' => $participation->status()->value,
            'start_time' => $participation->getStartTime(),
            'end_time' => $participation->getEndTime(),
        ]);
    }
}
