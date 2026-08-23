<?php

namespace App\Infrastructure\Participation;

use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationRepository;

class EloquentParticipationRepository implements ParticipationRepository
{
    public function findByActivityIdAndAssistantId(string $activityId, string $assistantId): ?Participation
    {
        $foundParticipation = EloquentParticipation::where([
            'activity_id' => $activityId,
            'assistant_id' => $assistantId,
        ])->first();

        if (!$foundParticipation) {
            return null;
        }

        return Participation::fromDatabase(
            $foundParticipation->id,
            $foundParticipation->assistant_id,
            $foundParticipation->activity_id,
            $foundParticipation->required_hours,
            $foundParticipation->start_time->toImmutable(),
            $foundParticipation->end_time?->toImmutable()
        );
    }

    public function create(Participation $participation): void
    {
        EloquentParticipation::create([
            'id' => $participation->getId()->value(),
            'activity_id' => $participation->getActivityId(),
            'assistant_id' => $participation->getAssistantId(),
            'required_hours' => $participation->getRequiredHours()->getTotalHours(),
            'status' => $participation->status()->value,
            'start_time' => $participation->getStartTime(),
            'end_time' => $participation->getEndTime(),
        ]);
    }

    public function update(Participation $participation): void
    {
        EloquentParticipation::findOrFail($participation->getId()->value())
            ->update([
                'required_hours' => $participation->getRequiredHours()->getTotalHours(),
                'status' => $participation->status()->value,
                'start_time' => $participation->getStartTime(),
                'end_time' => $participation->getEndTime(),
            ]);
    }
}
